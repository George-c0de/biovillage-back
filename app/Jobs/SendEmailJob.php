<?php

namespace App\Jobs;

use App\Mail\BaseMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendEmailJob extends BaseJob
{

    public $mailClass;
    public $mailData;
    public $to;


    /**
     * Create a new job instance.
     *
     * @param string $to
     * @param $class
     * @param $data
     */
    public function __construct(string $to, string $class, $data = null)
    {
        $this->mailClass = $class;
        $this->mailData = $data;
        $this->to = $to;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->to)
            ->send(new $this->mailClass($this->mailData));
    }
}
