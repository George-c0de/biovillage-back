<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Базовый класс писем
 */
class BaseMail extends Mailable
{
    use Queueable, SerializesModels;
}
