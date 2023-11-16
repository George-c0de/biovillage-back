<?php

namespace App\Service\PaymentGateway;


interface IPaymentNotificationContext
{
    /**
     * Return client from notification
     * @return mixed
     */
    public function getClientId();

    /**
     * Return order from notification
     * @return mixed
     */
    public function getOrderId();

    /**
     * Return id of payment in gateway
     * @return mixed
     */
    public function getPaymentId();

    /**
     * Return notification details
     * @return mixed
     */
    public function getDetails();

    /**
     * Context as string
     * @return string
     */
    public function dump() : string;

    /**
     * Payment amount
     * @return int
     */
    public function getAmount(): int;

    /**
     * Currency
     * @return string
     */
    public function getCurrency() : string;
}