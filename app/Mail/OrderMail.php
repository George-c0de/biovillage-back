<?php

namespace App\Mail;

use App\Service\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class Subscribe
 * @package App\Mail
 */
class OrderMail extends BaseMail
{

    public $subject = 'Новый заказ';
    public $order;

    /**
     * HelloMail constructor.
     * @param $data
     *  orderId
     */
    public function __construct($data)
    {
        $this->order = OrderService::searchOne([
            'id' => $data['orderId']
        ]);
    }

    /**
     * Build mail
     */
    public function build()
    {
        return $this->text('mail.order', [
            'order' => $this->order
        ]);
    }
}
