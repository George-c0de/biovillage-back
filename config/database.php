<?php

return [


    'default' => env('DB_CONNECTION', 'main'),


    'connections' => [

        'main' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => env('DB_SCHEMA', 'main'),
            'sslmode' => 'prefer',
        ],

        'log' => [
            'driver' => 'pgsql',
            'host' => env('DB_LOGS_HOST', '127.0.0.1'),
            'port' => env('DB_LOGS_PORT', '5432'),
            'database' => env('DB_LOGS_DATABASE', 'forge'),
            'username' => env('DB_LOGS_USERNAME', 'forge'),
            'password' => env('DB_LOGS_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => env('DB_SCHEMA', 'main'),
            'sslmode' => 'prefer',
        ],
    ],


    'migrations' => 'migrations',



    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

        'queue' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_QUEUE_DB', 2),
        ],

    ],

];
