<?php

namespace App\Console\Commands;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\ImageModel;
use App\Service\CustomModelService;
use App\Service\KmlMapService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class KmlLoadFileCommand extends Command
{
    protected $signature = 'da:load-kml {--file=}';

    protected $description = 'Load kml file';

    public function handle()
    {
        $s = new KmlMapService();
        $areas = $s->parseKmlFile($this->option('file'));

        dd(
            $areas[0]['name'],
            DbHelper::arrayToPgPath($areas[0]['coords'])
        );
    }

}
