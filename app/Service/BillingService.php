<?php

namespace App\Service;
use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\Auth\Client;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\SettingsModel;
use App\Service\PaymentGateway\IGateway;
use App\Service\PaymentGateway\IPaymentNotificationContext;
use Illuminate\Support\Facades\DB;

/**
 * Billing
 */
class BillingService
{

    // Supported gateways
    const GATEWAY_YOOKASSA = 'yk';
    const GATEWAY_STRIPE = 'st';
    const GATEWAY_PAYPAL = 'pl';

    // Currencies
    const CURRENCY_RUB = 'RUB';
    const CURRENCY_USD = 'USD';

    // Support payments gateways
    const SUPPORTED_GATEWAYS = [
        self::GATEWAY_YOOKASSA => \App\Service\PaymentGateway\YooKassa\Gateway::class
    ];

    /**
     * Gateway factory
     */
    public function gateway() : IGateway {
        $gateway = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_GATEWAY
        );
        $class = self::SUPPORTED_GATEWAYS[ $gateway ] ??
            self::SUPPORTED_GATEWAYS[ self::GATEWAY_YOOKASSA ];
        return new $class();
    }

    /**
     * Payment authorized. Order is prepared to purchase
     * @param $payment
     * @param IPaymentNotificationContext $notification
     */
    public function onAuthorizePaymentNotify($payment, IPaymentNotificationContext $notification)
    {
        DB::transaction(function () use ($payment, $notification) {
            $payment->data .= $notification->dump();
            $payment->save();
            OrderService::changeStatus(
                $payment->order,
                OrderModel::STATUS_PLACED,
                true
            );
        });
    }

    /**
     * Payment done
     * @param $payment
     * @param IPaymentNotificationContext $notification
     */
    public function onSuccessPaymentNotify($payment, IPaymentNotificationContext $notification)
    {
        DB::transaction(function () use ($payment, $notification) {
            $payment->status = PaymentModel::PAYMENT_DONE;
            $payment->data .= $notification->dump();
            $payment->madeAt = DbHelper::currTs();
            $payment->save();

            // Check paid and notify sums
            if($payment->total != $notification->getAmount()) {
                Utils::raise('Notify sum and paid sum not equal', [
                    'paymentId' => $payment->id,
                    'paymentSum' => $payment->total,
                    'notifySum' => $notification->getAmount(),
                    'paidSum' => $payment->total
                ]);
            }

            OrderService::changeStatus(
                $payment->order,
                OrderModel::STATUS_FINISHED,
                true
            );
        });
    }

    /**
     * Cancel notify
     * @param $payment
     * @param IPaymentNotificationContext $notification
     */
    public function onCancelPaymentNotify($payment, IPaymentNotificationContext $notification)
    {
        DB::transaction(function () use ($payment, $notification) {
            # Thin thing
            $payment->status = PaymentModel::PAYMENT_CANCEL;
            $payment->data .= $notification->dump();
            $payment->canceledAt = DbHelper::currTs();
            $payment->save();

            OrderService::changeStatus(
                $payment->order,
                OrderModel::STATUS_CANCEL,
                true
            );
        });
        resolve('PaymentStatusHandler')->cancelPayment($payment);
    }

    /**
     * Refund notify
     * @param $payment
     * @param IPaymentNotificationContext $notification
     */
    public function onRefundPaymentNotify($payment, IPaymentNotificationContext $notification)
    {
        DB::transaction(function () use ($payment, $notification) {
            # Thin thing
            $payment->status = PaymentModel::PAYMENT_DONE;
            $payment->data .= $notification->dump();
            $payment->madeAt = DbHelper::currTs();
            $payment->save();

            OrderService::changeStatus(
                $payment->order,
                OrderModel::STATUS_CANCEL,
                true
            );
        });
    }

    /**
     * Complete order and primary payment
     * @param $order - OrderModel | orderId
     * @param int $confirmSum
     * @throws \Exception
     */
    public function completeOrder($order, $confirmSum = 0) {

        // Get order and check one
        $order = OrderModel::orderInstance($order);
        if(!OrderStatusNavigator::canNavigate($order->status, OrderModel::STATUS_FINISHED)) {
            Utils::raise(
                'Cant change status from ' . $order->status . ' to ' . OrderModel::STATUS_FINISHED
            );
        }

        // Confirm. Transfer money to account.
        $primaryPayment = $order->primaryPayment();
        if(empty($confirmSum)) {
            $confirmSum = OrderService::calcPurchasedProductsSum($order, true);
            $confirmSum += $order->deliverySum;
            // No purchased. This is error
            if(empty($confirmSum)) {
                Utils::raise('Sum of real purchased is zero');
            }
        }

        DB::transaction(function() use ($primaryPayment, $confirmSum, $order) {

            // May be a very long transaction. Hm...
            PaymentService::confirmPayment($primaryPayment, $confirmSum);

            // Partial virtual refund
            // Create in any way. In case gateway payment or any methods
            // In case apple or google pay this case need to rewrite
            $refundSum = $order->total - $confirmSum;
            if($refundSum > 1) {
                PaymentService::fastPartialRefund($primaryPayment, $refundSum);
            }

            // Add bonuses for order
            resolve('Bonuses')->addBonusesForOrderAndReferral($order);

            // Write off bonuses for gift
            resolve('Bonuses')->writeOffBonusesForOrder($order);

            // Change order status
            OrderService::changeStatus($order, OrderModel::STATUS_FINISHED, true);

            resolve('Store')->orderingCompleted($order);
        });
    }

    /**
     * Cancel order
     * @param $order - OrderModel | orderId
     */
    public function cancelOrder($order) {

        // Get order and check one
        $order = OrderModel::orderInstance($order);
        if(!OrderStatusNavigator::canNavigate($order->status, OrderModel::STATUS_CANCEL)) {
            Utils::raise(
                'Cant change status from ' . $order->status . ' to ' . OrderModel::STATUS_FINISHED
            );
        }

        // Confirm. Transfer money to account.
        PaymentService::cancelPayment($order->primaryPayment());

        // Change order status
        OrderService::changeStatus($order, OrderModel::STATUS_CANCEL, true);

        resolve('Store')->orderingCancel($order);
    }

    /**
     * Cancel and refund order
     * @param $order - OrderModel | orderId
     */
    public function cancelAndRefundOrder($order) {

        // Get order and check one
        $order = OrderModel::orderInstance($order);
        if($order->status != OrderModel::STATUS_FINISHED) {
            Utils::raise('Cancel and refund only finished orders', [
                'orderId' => $order->id,
                'orderStatus' => $order->status
            ]);
        }

        // Calc paid sum
        $refundSum = PaymentService::calcPaidOrderSum($order->id);
        PaymentService::refundPayment($order->primaryPayment(), $refundSum);

        // Change order status
        OrderService::changeStatus($order, OrderModel::STATUS_CANCEL);

        resolve('Store')->orderingCancel($order);
    }


}