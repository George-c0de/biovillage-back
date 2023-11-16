<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RoutePatternsServiceProvider extends ServiceProvider
{
    public function boot() {
        Route::pattern('id', '[0-9]+');
        Route::pattern('any', '.*');
    }

    public function register() {
    }
}
