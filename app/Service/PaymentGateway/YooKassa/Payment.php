<?php

namespace App\Service\PaymentGateway\YooKassa;

use YooKassa\Request\Payments\AbstractPaymentResponse;
use YooKassa\Request\Payments\CreatePaymentResponse;
use DateTime;

class Payment implements \App\Service\PaymentGateway\IPayment {


    /**
     * Saved YooKassa Response
     * @var $yooKassaResp
     */
    private $yooKassaResp;

    /**
     * Payment constructor.
     * @param AbstractPaymentResponse $yooKassaResponse
     */
    public function __construct(AbstractPaymentResponse $yooKassaResponse)
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
     * Payment method
     * @return string
     */
    public function method(): string
    {
        return $this->yooKassaResp->getPaymentMethod()->getId();
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
     * Return payment confirmation url
     * @return string
     */
    public function confirmationUrl(): string
    {
        $confirmation = $this->yooKassaResp->getConfirmation();
        if(!empty($confirmation)) {
            return $confirmation->getConfirmationUrl();
        }
        return '';
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