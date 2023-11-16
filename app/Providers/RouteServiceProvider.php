<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\URL;

class RouteServiceProvider extends ServiceProvider
{


    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::prefix('admin')
            ->middleware('web')
            ->namespace('App\Http\Controllers\Back')
            ->group(base_path('routes/back.php'));

        Route::middleware('web')
            ->namespace('App\Http\Controllers\Front')
            ->group(base_path('routes/front.php'));
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace('App\Http\Controllers\Api')
             ->group(base_path('routes/api.php'));
    }
}
