<?php

namespace App\Service\PaymentGateway\YooKassa;

use YooKassa\Request\Payments\AbstractPaymentResponse;
use YooKassa\Request\Payments\CreatePaymentResponse;
use DateTime;
use YooKassa\Request\Refunds\AbstractRefundResponse;

class Refund implements \App\Service\PaymentGateway\IRefund {


    /**
     * Saved YooKassa Response
     * @var $yooKassaResp
     */
    private $yooKassaResp;

    /**
     * Payment constructor.
     * @param AbstractRefundResponse $yooKassaResponse
     */
    public function __construct(AbstractRefundResponse $yooKassaResponse)
    {
        $this->yooKassaResp = $yooKassaResponse;
    }

    /**
     * Payment id
     * @return string
     */
    public function id(): string
    {
        return $this->yooKassaResp->getId();
    }

    /**
     * Status of payment
     * @return string
     */
    public function status(): string
    {
        return $this->yooKassaResp->getStatus();
    }

    /**
     * Amount of payment
     * @return int
     */
    public function amount(): int
    {
        return $this->yooKassaResp->getAmount()->getIntegerValue();
    }

    /**
     * ISO code of currency
     * @return string
     */
    public function currency(): string
    {
        return $this->yooKassaResp->getAmount()->getCurrency();
    }

    /**
     * Payment date
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return $this->yooKassaResp->getCreatedAt();
    }

    /**
     * All payment details
     * @return array
     */
    public function details(): array
    {
        return $this->yooKassaResp->jsonSerialize();
    }

    /**
     * Dump payment response as string
     * @return string
     */
    public function dump(): string {
        // This is safe dump
        try {
            return json_encode(
                $this->details(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}