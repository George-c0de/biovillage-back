<?php

namespace App\Service\PaymentGateway\YooKassa;

use App\Helpers\Utils;
use App\Models\PaymentModel;
use App\Models\SettingsModel;
use App\Searchers\OrderPaymentsSearcher;
use App\Service\BillingService;
use App\Service\PaymentGateway\IPayment;
use App\Service\PaymentGateway\IPaymentNotificationContext;
use App\Service\PaymentGateway\IPaymentNotificationValidator;
use App\Service\PaymentGateway\IRefund;
use App\Service\PaymentService;
use App\Service\SettingsService;
use Illuminate\Support\Str;
use YooKassa\Model\Notification\NotificationCanceled;
use YooKassa\Model\Notification\NotificationRefundSucceeded;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Client;
use Illuminate\Support\Facades\Log;

class Gateway implements \App\Service\PaymentGateway\IGateway
{

    /**
     * Notification validator
     * @return IPaymentNotificationValidator
     */
    public function getNotificationValidator(): IPaymentNotificationValidator
    {
        return new PaymentNotificationValidator();
    }

    /**
     * Process notification
     * @param IPaymentNotificationContext $context
     * @return mixed
     */
    public function processNotification(IPaymentNotificationContext $context)
    {

        // Notify object
        $notify = $context->getDetails();

        // Model
        $payment = PaymentModel::where('extId', $context->getPaymentId())->first();
        if(empty($payment)) {
            Utils::raise('Payment not found', [
                'paymentId' => $context->getPaymentId()
            ]);
        }

        $billing = resolve('Billing');

        // Money held
        if($notify instanceof NotificationWaitingForCapture) {
            $billing->onAuthorizePaymentNotify($payment, $context);
            return;
        }

        // Money received
        if($notify instanceof NotificationSucceeded) {
            $billing->onSuccessPaymentNotify($payment, $context);
            return;
        }

        // Cancel payment
        if($notify instanceof NotificationCanceled) {
            $billing->onCancelPaymentNotify($payment, $context);
            return;
        }

        // Refund payment
        if($notify instanceof NotificationRefundSucceeded) {
            $billing->onRefundPaymentNotify($payment, $context);
        }
    }

    /**
     * Make payment notification context from data
     * @param array $data
     * @return IPaymentNotificationContext
     */
    public function getNotificationContext(array $data): IPaymentNotificationContext
    {
        return new PaymentNotificationContext($data);
    }

    /**
     * Detect need confirm payment
     * @param $method
     * @return bool
     */
    private function needConfirmation($method) {
        return !in_array(
            $method, [
                PaymentModel::METHOD_APPLE_PAY,
                PaymentModel::METHOD_GOOGLE_PAY
            ]);
    }

    /**
     * Create payment
     * @param $data
     *  token
     *  amount
     *  method
     *  desc
     *  returnUrl
     *  currency
     *  orderId
     * @return mixed
     */
    public function createPayment(array $data) : IPayment
    {
        Utils::raiseIfEmpty($data, ['token', 'amount', 'method', 'desc',
            'returnUrl', 'currency', 'orderId']);

        Log::debug('Create payment', $data);

        $shopId = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_SHOP_ID
        );
        $secret = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_SECRET_KEY
        );
        if(empty($shopId) || empty($secret)) {
            Utils::raise('Empty shop id or key');
        }

        // Create payment
        $paymentTemplate = [
            'payment_token' => $data['token'],
            'capture' => false,
            'metadata' => [
                'orderId' => $data['orderId'],
                'token' => env('PAYMENT_GATEWAY_SECURE_TOKEN', Str::random())
            ],
            'amount' => [
                'value' => sprintf('%.2f', $data['amount'] ),
                'currency' => $data['currency'],
            ],
            'description' => $data['desc']
        ];
        // Add confirmation object
        if($this->needConfirmation($data['method'])) {
            $paymentTemplate['confirmation'] = [
                'type' => 'redirect',
                'return_url' => $data['returnUrl']
            ];
        }

        $client = new Client();
        $client->setAuth($shopId, $secret);
        $paymentResponse = $client->createPayment($paymentTemplate);
        return new Payment($paymentResponse);
    }

    /**
     * Confirm payment in gateway
     * @param $payment - This is external payment id
     * @param $value
     * @param $currencyCode
     * @return IPayment
     */
    public function confirmPayment($payment, $value, $currencyCode): IPayment {

        if(empty($payment)) {
            Utils::raise('No payment');
        }
        if(empty($value)) {
            Utils::raise('No confirm payment amount');
        }
        if(empty($currencyCode)) {
            Utils::raise('No currency code');
        }

        $shopId = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_SHOP_ID
        );
        $secret = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_SECRET_KEY
        );
        if(empty($shopId) || empty($secret)) {
            Utils::raise('Empty shop id or key');
        }

        // Create payment
        $client = new Client();
        $client->setAuth($shopId, $secret);
        $captureResponse = $client->capturePayment([
            'amount' => [
                'value' => $value,
                'currency' => $currencyCode
            ]
        ], $payment);
        return new Payment($captureResponse);
    }

    /**
     * Cancel payment if payment is not completed
     * @param $payment
     * @return IPayment
     */
    public function cancelPayment($payment): IPayment
    {
        if(empty($payment)) {
            Utils::raise('No payment');
        }

        $shopId = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_SHOP_ID
        );
        $secret = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_SECRET_KEY
        );
        if(empty($shopId) || empty($secret)) {
            Utils::raise('Empty shop id or key');
        }

        // Cancel payment
        $client = new Client();
        $client->setAuth($shopId, $secret);
        $cancelResponse = $client->cancelPayment($payment);
        return new Payment($cancelResponse);
    }


    /**
     * Refund payment if payment is completed
     * @param $payment
     * @param $value
     * @param $currencyCode
     * @return IPayment
     */
    public function refundPayment($payment, $value, $currencyCode): IRefund
    {
        if(empty($payment)) {
            Utils::raise('No payment');
        }

        $shopId = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_SHOP_ID
        );
        $secret = SettingsService::getSettingValue(
            SettingsModel::SETTING_PAYMENT_SECRET_KEY
        );
        if(empty($shopId) || empty($secret)) {
            Utils::raise('Empty shop id or key');
        }

        // Create refund
        $client = new Client();
        $client->setAuth($shopId, $secret);
        $refundResponse = $client->createRefund([
            'payment_id' => $payment,
            'amount' => [
                'value' => $value,
                'currency' => $currencyCode
            ]
        ]);
        return new Refund($refundResponse);
    }
}