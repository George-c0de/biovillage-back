<?php

namespace Packages\Store;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class StoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::prefix('api')->group(function() {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }
}
