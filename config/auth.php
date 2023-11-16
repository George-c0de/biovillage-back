<?php

return [

    'defaults' => [
        'guard' => 'client',
        'passwords' => 'clients',
    ],

    'guards' => [
        // Guard web не удалять нужен для корректной работы Laravel Sanctum
        'web' => [
            'driver' => 'session',
            'provider' => 'clientModel',
        ],

        'client' => [
            'driver' => 'session',
            'provider' => 'clientModel',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'adminModel',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'adminModel',
            'hash' => false,
        ],
    ],

    'providers' => [
        'clientModel' => [
            'driver' => 'eloquent',
            'model' => App\Models\Auth\Client::class,
        ],
        'adminModel' => [
            'driver' => 'eloquent',
            'model' => App\Models\Auth\Admin::class,
        ],
    ],

    'passwords' => [
        'clients' => [
            'provider' => 'clientModel',
            'table' => 'password_resets',
            'expire' => 60,
        ],
        'admins' => [
            'provider' => 'adminModel',
            'table' => 'password_resets',
            'expire' => 60,
        ]
    ],

];
