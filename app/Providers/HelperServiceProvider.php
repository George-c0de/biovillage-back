<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{

    /**
    * Register services.
    */
    public function register() {
        // Добавим хелпер для работы со специфичными для postgesql типами данных
        //require_once app_path().'/Helpers/DbHelper.php';
    }
}