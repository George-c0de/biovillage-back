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
class UpdateBirthdayMail extends BaseMail
{

    public $subject = 'Запрос на обновление даты рождения';
    protected $clientId;
    protected $birthday;

    /**
     * HelloMail constructor.
     * @param $name
     */
    public function __construct($data)
    {
        $this->clientId = $data['clientId'];
        $this->birthday = $data['birthday'];
    }

    /**
     * Build mail
     */
    public function build()
    {
        return $this->text('mail.change-birthday', [
            'clientId' => $this->clientId,
            'birthday' => $this->birthday
        ]);
    }
}
