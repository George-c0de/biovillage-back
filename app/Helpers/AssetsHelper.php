<?php

use Illuminate\Support\Str;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * Файл загружается в composer.json
 */

if (! function_exists('foasset')) {
    function foasset($path, $secure = null) {
        if(!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        return mix('front' . $path, $secure);
    }
}

if (! function_exists('boasset')) {
    function boasset($path, $secure = null) {
        if(!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        return mix('back' . $path, $secure);
    }
}

/**
 * Форматтер для текущей локали
 */
if (! function_exists('locale')) {
    function locale() {
        return app()->make('locale');
    }
}

/**
 * Домен приложения
 */
if(!function_exists('domain')) {
    function domain() {
        return env('APP_DOMAIN', 'localhost');
    }
}