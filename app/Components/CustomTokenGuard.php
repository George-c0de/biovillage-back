<?php

namespace App\Components;

use Illuminate\Auth\TokenGuard;

/**
 * Свой guard с возможностью настройки имени параметра передающего токен и поля в БД
 */
class CustomTokenGuard extends TokenGuard {

    public function __construct(\Illuminate\Contracts\Auth\UserProvider $provider, \Illuminate\Http\Request $request, string $inputKey = 'api_token', string $storageKey = 'api_token')
    {
        parent::__construct($provider, $request, $inputKey, $storageKey);
    }
}