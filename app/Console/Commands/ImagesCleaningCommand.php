<?php

namespace App\Console\Commands;

use App\Models\ImageModel;
use App\Service\CustomModelService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * Команда очистки удаленных файлов картинок
 */
class ImagesCleaningCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:cleaning {--chunk=}';

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
     * @return int
     */
    public function handle()
    {
        $chunkSelectSize = $this->option('chunk') ? $this->option('chunk') : 1000;

        $countTotal = ImageModel::onlyTrashed()
            ->where('isDeleted', false)
            ->count();

        if($countTotal){
            $ids = [];
            $processDbRow = 0;

            do {
                $imagesTrashed = ImageModel::onlyTrashed()
                    ->where('isDeleted', false)
                    ->offset(0)
                    ->limit($chunkSelectSize)
                    ->get();

                if (!$imagesTrashed) break;

                $imagesTrashed = $imagesTrashed->toArray();
                foreach ($imagesTrashed as $image) {
                    Storage::disk('public')->delete($image['src']);

                    if (Arr::has($image, 'srcThumb')) {
                        Storage::disk('public')->delete($image['srcThumb']);
                    }
                    $ids[] = $image['id'];
                }

                $fill = [];
                foreach ($ids as $id) {
                    $fillItem['id'] = $id;
                    $fillItem['isDeleted'] = true;
                    $fill[] = $fillItem;
                }
                $productsCustomModel = new CustomModelService(with(new ImageModel())->getTable());
                $productsCustomModel->updateMultiple([
                    ['id' => 'type:integer'],
                    ['isDeleted' => 'type:boolean']
                ], $fill);

                $ids = [];
                $processDbRow += $chunkSelectSize;
            } while ($processDbRow < $countTotal);
        }
    }
}
