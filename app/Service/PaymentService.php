<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\Auth\Client;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\SettingsModel as SM;
use App\Searchers\OrderItemsSearcher;
use App\Searchers\OrderPaymentsSearcher;
use App\Searchers\ProductSearcher;
use App\Service\Images\ImageService;
use App\Service\PaymentGateway\IPaymentNotificationContext;
use Faker\Provider\Payment;
use Hamcrest\Util;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;

/**
 * Payment Service.
 *
 * @package App\Service\Section
 */
class PaymentService
{
    /**
     * Get last payment type of client
     * @param $clientId
     * @return string - Payment type
     */
    public static function getClientLastPayMethod($clientId) {
        $s = new OrderPaymentsSearcher();
        $lastPayments = $s->search([
            'clientId' => $clientId,
            'limit' => 1,
            'sortDirect' => 'DESC'
        ]);
        if(empty($lastPayments)) {
            return PaymentModel::METHOD_CASH;
        }
        return $lastPayments[0]['method'];
    }

    /**
     * Create payment by order
     * @param $params
     *  orderId
     *  paymentPrimaryMethod
     *  paymentPrimaryData
     *  total
     *  paymentToken
     *  transaction
     * @return mixed
     */
    public static function createPayment($params) {

        Utils::raiseIfEmpty($params, ['orderId', 'paymentPrimaryMethod', 'total']);

        $p = new PaymentModel();
        $p->createdAt = DbHelper::currTs();
        $p->transaction = $params['transaction'] ?? PaymentModel::TRANSACTION_PAY;
        $p->method = $params['paymentPrimaryMethod'];
        $p->status = $params['status'] ?? PaymentModel::PAYMENT_WAIT;
        $p->orderId = $params['orderId'];
        $p->total = $params['total'];
        $p->data = $params['paymentPrimaryData'] ?? '';
        $p->paymentToken = $params['paymentToken'] ?? '';
        $p->currencyCode = SettingsService::getSettingValue(
            SM::SETTING_PAYMENT_CURRENCY_CODE
        );
        $p->save();

        if(static::throughGateway($params['paymentPrimaryMethod'])) {
            $params['currency'] = $p->currencyCode;
            $params['returnUrl'] = SettingsService::getSettingValue(
                SM::SETTING_PAYMENT_RETURN_URL);
            $payResult = static::payThroughGateway($params);
            $p->extId = $payResult->id();
            $p->confirmation = $payResult->confirmationUrl();
            $p->data .= $payResult->dump();
            $p->save();
        }

        return $p;
    }

    /**
     * Create paid refund payment
     * @param PaymentModel $payment
     * @param int $refundSum
     * @return PaymentModel
     * @throws \Exception
     */
    public static function fastPartialRefund(PaymentModel $payment, int $refundSum) {

        $paidSum = self::calcPaidOrderSum($payment->orderId);
        if($refundSum > $paidSum ) {
            Utils::raise('Refund sum more than paid sum', [$refundSum, $paidSum]);
        }

        // Copy new payment
        $p = $payment->replicate();
        $p->parentId = $payment->id;
        $p->transaction = PaymentModel::TRANSACTION_REFUND;
        $p->createdAt = DbHelper::currTs();
        $p->refundedAt = DbHelper::currTs();
        $p->status = PaymentModel::PAYMENT_WAIT;
        $p->createdAt = DbHelper::currTs();
        $p->madeAt = DbHelper::currTs();
        $p->total = 0 - abs($refundSum);
        $p->status = PaymentModel::PAYMENT_DONE;
        $p->data = '';
        $p->paymentToken = '';
        $p->extId = '';
        $p->data = '';
        $p->confirmation = '';
        $p->push();

        return $p;
    }

    /**
     * Confirm payment. Transfer hold money to account
     * @param PaymentModel $payment
     * @param int $sum - Used for gateway payment
     */
    public static function confirmPayment(PaymentModel $payment, $sum = 0) {
        if($payment->status == PaymentModel::PAYMENT_CANCEL) {
           Utils::raise('Confirm only wait payment');
        }
        if($payment->transaction != PaymentModel::TRANSACTION_PAY) {
            Utils::raise('Refund only payment transaction');
        }

        if(!static::throughGateway($payment->method)) {
            $payment->madeAt = DbHelper::currTs();
            $payment->status = PaymentModel::PAYMENT_DONE;
            $payment->save();
            return;
        }
        if(empty($payment->extId)) {
            Utils::raise('ExtId not exists', [
                'paymentId' => $payment->id,
                'orderId' => $payment->orderId
            ]);
        }

        if(empty($sum)) {
            $sum = $payment->total;
        }

        $confirmResponse = resolve('Billing')->gateway()->confirmPayment(
            $payment->extId,
            $sum,
            $payment->currencyCode
        );

        // Update payment
        $payment->status = PaymentModel::PAYMENT_DONE;
        $payment->data .= $confirmResponse->dump();
        $payment->madeAt = DbHelper::currTs();
        $payment->save();
    }

    /**
     * Execute payment
     * @param $params array
     *  currency
     *  paymentToken
     *  total
     *  method || paymentPrimaryType
     *  orderId
     *  returnUrl
     * @return PaymentGateway\IPayment|mixed
     */
    private static function payThroughGateway($params) {
        return resolve('Billing')->gateway()->createPayment([
            'token' => $params['paymentToken'],
            'amount' => $params['total'],
            'method' => $params['method'] ?? $params['paymentPrimaryMethod'],
            'orderId' => $params['orderId'],
            'desc' => trans('billing.paymentDescription', [
                'orderId' => $params['orderId'],
                'token' => env('PAYMENT_GATEWAY_SECURE_TOKEN')
            ]),
            'returnUrl' => $params['returnUrl'],
            'currency' => $params['currency']
        ]);
    }

    /**
     * Determine gateway pay
     * @param $paymentMethod
     * @return bool
     */
    public static function throughGateway($paymentMethod) {
        return in_array($paymentMethod, [ PaymentModel::METHOD_APPLE_PAY,
            PaymentModel::METHOD_GOOGLE_PAY, PaymentModel::METHOD_BANK_CARD]);
    }


    /**
     * Cancel payment
     * @param $payment
     */
    public static function cancelPayment(PaymentModel $payment) {

        // Check payment status
        if($payment->status == PaymentModel::PAYMENT_DONE) {
            Utils::raise('Cant cancel done payment', [
                'paymentId' => $payment->id,
                'orderId' => $payment->orderId
            ]);
        }

        // Cant cancel completed payment which paid via gateway
        if(static::throughGateway($payment->method)) {
            if($payment->transaction == PaymentModel::TRANSACTION_PAY) {
                if($payment->status == PaymentModel::PAYMENT_DONE) {
                    Utils::raise('Cant cancel completed payment');
                }
                if(empty($payment->extId)) {
                    Utils::raise('Payment has not extId');
                }
                $cancelResponse = resolve('Billing')
                    ->gateway()
                    ->cancelPayment($payment->extId);
                $payment->data .= $cancelResponse->dump();
            }
        }

        // Change payment status
        $payment->status = PaymentModel::PAYMENT_CANCEL;
        $payment->canceledAt = DbHelper::currTs();
        $payment->save();
    }

    /**
     * Refund payment
     * @param PaymentModel $payment
     * @param int $refundSum
     * @throws \Exception
     */
    public static function refundPayment(PaymentModel $payment, $refundSum = 0) {
        if($payment->transaction == PaymentModel::TRANSACTION_REFUND) {
            Utils::raise('Refund only payment');
        }
        if($payment->status != PaymentModel::PAYMENT_DONE) {
            Utils::raise('Refund only paid');
        }
        if(empty($refundSum)) {
            $refundSum = $payment->total;
        }

        // Copy new payment
        $p = $payment->replicate();
        $p->parentId = $payment->id;
        $p->transaction = PaymentModel::TRANSACTION_REFUND;
        $p->createdAt = DbHelper::currTs();
        $p->refundedAt = DbHelper::currTs();
        $p->status = PaymentModel::PAYMENT_WAIT;
        $p->total = 0 - abs($refundSum);
        $p->data = '';
        $p->paymentToken = '';
        $p->extId = '';
        $p->data = '';
        $p->confirmation = '';

        if(static::throughGateway($payment->method)) {
            $refundResponse = resolve('Billing')->gateway()->refundPayment(
                $payment->extId, $refundSum, $payment->currencyCode
            );
            $p->extId = $refundResponse->id();
            $p->data = $refundResponse->dump();
        }

        $p->push();
    }

    /**
     * Calc order sum what not refunded
     * @param $order
     * @return null
     */
    public static function calcPaidOrderSum($order) {

        // If model
        if(is_object($order)) {
            $order = $order->id;
        }

        $s = new OrderPaymentsSearcher();
        return $s->calcOrderPayments($order);
    }
}
