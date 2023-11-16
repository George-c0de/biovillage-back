<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Пример письма для проверки работы
 * Class HelloMail
 * @package App\Mail
 */
class HelloMail extends BaseMail
{

    public $subject = 'Приветственное письмо';
    protected $name;

    /**
     * HelloMail constructor.
     * @param $name
     */
    public function __construct($data)
    {
        $this->name = $data['name'];
    }

    /**
     * @return HelloMail
     */
    public function build()
    {
        return $this->view('mail.hello', [
            'name' => $this->name
        ]);
    }
}
