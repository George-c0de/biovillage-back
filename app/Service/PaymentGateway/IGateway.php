<?php

namespace App\Service\PaymentGateway;


interface IGateway
{

    /**
     * Create payment. Hold money.
     * @param $data
     * @return mixed
     */
    public function createPayment(array $data) : IPayment;

    /**
     * Confirm payment
     * @param $payment
     * @param $value
     * @param $currencyCode
     * @return IPayment
     */
    public function confirmPayment($payment, $value, $currencyCode) : IPayment;

    /**
     * Cancel payment if payment is not completed
     * @param $payment
     * @return IPayment
     */
    public function cancelPayment($payment) : IPayment;

    /**
     * Refund payment if payment is completed
     * @param $payment
     * @param $value
     * @param $currencyCode
     * @return IPayment
     */
    public function refundPayment($payment, $value, $currencyCode) : IRefund;

    /**
     * Notification validator
     * @return IPaymentNotificationValidator
     */
    public function getNotificationValidator() : IPaymentNotificationValidator;

    /**
     * Make payment notification context from data
     * @param array $data
     * @return IPaymentNotificationContext
     */
    public function getNotificationContext(array $data) : IPaymentNotificationContext;

    /**
     * Process notification
     * @param IPaymentNotificationContext $context
     * @return mixed
     */
    public function processNotification(IPaymentNotificationContext $context);
}