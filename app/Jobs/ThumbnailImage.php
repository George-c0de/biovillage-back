<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Создание миниатюры для изображения
 * метод создает миниатюру и перезаписывает файл
 */
class ThumbnailImage implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $imagePath;
    private $size;

    /**
     * Create a new job instance.
     *
     * @param string $imagePath
     * @param string $size '64x64' - ширина высота
     */
    public function __construct(string $imagePath, string $size) {
        $this->$imagePath = $imagePath;
        $this->size = $size;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        exec(
            'convert ' .
            escapeshellarg($this->imagePath) .
            ' -resize ' .
            $this->size . '\> ' .
            escapeshellarg($this->imagePath)
        );
    }
}
