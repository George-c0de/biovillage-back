<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class Subscribe
 * @package App\Mail
 */
class SubscribeMail extends BaseMail
{

    public $subject = 'Ваш промокод';
    protected $promoCode;

    /**
     * HelloMail constructor.
     * @param $name
     */
    public function __construct($data)
    {
        $this->promoCode = $data['promoCode'];
    }

    /**
     * Build mail
     */
    public function build()
    {
        return $this->text('mail.subscribe', [
            'promoCode' => $this->promoCode
        ]);
    }
}
