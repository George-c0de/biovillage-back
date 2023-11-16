<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FileCleaning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:clear {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->argument('path');
        exec(
            'find ' . ' ' .
            $path . ' ' .
            '-mindepth 1 -type f -mtime +1 -delete'
        );
    }
}
