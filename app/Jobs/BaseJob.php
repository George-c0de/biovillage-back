<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param $message
     * @param array $params
     */
    protected function consoleLog($message, $params = []) {
        Log::channel('stdout')->debug($message, $params);
    }

    /**
     * Отправка ошибок очереди в sentry
     * @param \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        // Send exception data to sentry.io
        // It should catch it by default since it throws an exception
        // But you can force a report manually
    }
}
