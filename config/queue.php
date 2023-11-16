<?php

return [

    'default' => env('QUEUE_CONNECTION', 'redis'),

    'connections' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'queue',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => null,
        ],

    ],

    'failed' => [
        'database' => env('DB_CONNECTION', 'main'),
        'table' => 'failed_jobs',
    ],

];