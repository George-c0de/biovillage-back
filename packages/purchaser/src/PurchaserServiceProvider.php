<?php

namespace Packages\Purchaser;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PurchaserServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::prefix('api')->group(function() {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }
}
