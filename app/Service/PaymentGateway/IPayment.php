<?php

namespace App\Service\PaymentGateway;


use DateTime;

interface IPayment
{
    /**
     * Payment id
     * @return string
     */
    public function id() : string;

    /**
     * Status of payment
     * @return string
     */
    public function status() : string;

    /**
     * Amount of payment
     * @return int
     */
    public function amount() : int;

    /**
     * ISO code of currency
     * @return string
     */
    public function currency() : string;

    /**
     * Payment method
     * @return string
     */
    public function method() : string;

    /**
     * Payment date
     * @return DateTime
     */
    public function createdAt() : DateTime;

    /**
     * Return all payment details
     * @return array
     */
    public function details();

    /**
     * Dump payment as string
     * @return string
     */
    public function dump(): string;

    /**
     * Return confirmation of payment url
     * @return string
     */
    public function confirmationUrl(): string;
}