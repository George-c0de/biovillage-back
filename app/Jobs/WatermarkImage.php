<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Добавление водяного знака на изображение
 */
class WatermarkImage implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $watermarkPath = 'images/watermark.png';
    private $watermarkOpacity = 0.8;
    private $imagePath;

    /**
     * Create a new job instance.
     *
     * @param string $imagePath
     */
    public function __construct(string $imagePath) {
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        exec(
            'magick ' .
            escapeshellarg($this->imagePath) .
            ' -set option:WMSIZE %[fx:w/2]x%[fx:h/2] \( ' .
            resource_path($this->watermarkPath) .
            ' -resize %[WMSIZE] -alpha set -channel A -evaluate multiply '. $this->watermarkOpacity .' +channel \) -gravity center -composite ' .
            escapeshellarg($this->imagePath)
        );
    }
}
