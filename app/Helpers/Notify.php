<?php

namespace App\Helpers;

use App\Jobs\SendEmailJob;
use App\Jobs\SendSmsJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;

/**
 * Отправка уведомлений разными способами
 */
class Notify {

    /**
     * Отправка письма через очередь
     * @param $to
     * @param $mailClass
     * @param $data
     * @param $onQueue
     */
    public static function sendMail($to, $mailClass, $data, $onQueue = 'mails') {
        SendEmailJob::dispatch($to, $mailClass, $data)->onQueue($onQueue);
    }

    /**
     * Отправить письмо немедленно. Без очереди.
     * @param $to
     * @param $mailClass
     * @param $data
     */
    public static function sendMailImmediately($to, $mailClass, $data) {
        with(new SendEmailJob($to, $mailClass, $data))->handle();
    }

    /**
     * Отрпавка СМС через очередь
     * @param $phone
     * @param $template
     * @param $data
     */
    public static function sendSms($phone, $template, $data = []) {
        SendSmsJob::dispatch($phone, $template, $data)->onQueue('sms');
    }

    /**
     * Отправить СМС сейчас
     * @param $phone
     * @param $template
     * @param $data
     */
    public static function sendSmsImmediately($phone, $template, $data = []) {
        with(new SendSmsJob($phone, $template, $data))->handle();
    }
}
