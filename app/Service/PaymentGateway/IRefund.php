<?php

namespace App\Service\PaymentGateway;


use DateTime;

interface IRefund
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

}