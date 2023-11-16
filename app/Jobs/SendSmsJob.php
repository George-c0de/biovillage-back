<?php

namespace App\Jobs;

use App;

use App\Helpers\Utils;
use Hamcrest\Util;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $smsTemplate;
    public $smsData;
    public $phone;

    /**
     * Create a new job instance.
     *
     * @param string $phone
     * @param string $template
     * @param $data
     */
    public function __construct(string $phone, string $template, $data = null)
    {
        $this->smsTemplate = $template;
        $this->smsData = $data ?? [];
        $this->phone = $phone;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        // Текст СМС
        $smsText = View::make($this->smsTemplate, $this->smsData)->render();


        // В прод. режиме шлем СМСки через шлюз SMSC
        if (App::environment() == 'production') {
            $http = new Client();
            $response = (string)$http->request(
                'GET',
                'https://smsc.ru/sys/send.php',
                [
                    'query' => [
                        'login' => env('SMSC_LOGIN'),
                        'psw' => env('SMSC_PSW'),
                        'phones' => $this->phone,
                        'mes' => $smsText
                    ],
                ]
            )->getBody();
            if (!preg_match('~^OK~', $response)) {
                Utils::raise($response);
            }
        } else {
            // В режиме разработчика - шлем письма
            Mail::raw($smsText, function($message) {
                $message->to('dev@dev.ru', $this->phone);
            });
        }
    }
}