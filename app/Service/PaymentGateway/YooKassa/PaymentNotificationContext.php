<?php

namespace App\Service\PaymentGateway\YooKassa;


use App\Service\PaymentGateway\IPaymentNotificationContext;
use Illuminate\Support\Arr;
use YooKassa\Model\Notification\NotificationFactory;
use YooKassa\Model\Notification\NotificationWaitingForCapture;

class PaymentNotificationContext implements IPaymentNotificationContext
{

    /**
     * @var array - Context data
     */
    private $data;

    /**
     * NotificationContext constructor.
     * @param $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return client from notification
     * @return mixed
     */
    public function getClientId()
    {
        return Arr::get($this->data, 'object.metadata.clientId');
    }

    /**
     * Return order from notification
     * @return mixed
     */
    public function getOrderId()
    {
        return Arr::get($this->data, 'object.metadata.orderId');
    }

    /**
     * Return notification details
     * @return mixed
     */
    public function getDetails()
    {
        $factory = new NotificationFactory();
        return $factory->factory($this->data);
    }

    /**
     * Context as string
     * @return string
     */
    public function dump(): string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Return id of payment in gateway
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->getDetails()->getObject()->id;
    }

    /**
     * Payment amount
     * @return int
     */
    public function getAmount(): int
    {
        return intval(
            $this
            ->getDetails()
            ->getObject()
            ->getAmount()
            ->getIntegerValue() / 100);
    }

    /**
     * Currency
     * @return string
     */
    public function getCurrency(): string
    {
        return $this
            ->getDetails()
            ->getObject()
            ->getAmount()
            ->getCurrency();
    }
}