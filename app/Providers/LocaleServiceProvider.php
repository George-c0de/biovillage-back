<?php

namespace App\Providers;

use App\Locale\RussianFormatter;
use App\Locale\EnglishFormatter;
use App\Locale\BaseFormatter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class LocaleServiceProvider extends ServiceProvider
{

    const SUPPORTED_LANGUAGES = [
        'ru',
        'en'
    ];

    public function register() {
        $this->app->singleton( 'locale', function ($app) {
            $class = [
                'ru' => RussianFormatter::class,
                'en' => EnglishFormatter::class
            ][$app->getLocale()] ?? RussianFormatter::class;
            return new $class();
        });
    }
}
