<?php

use Laravel\Horizon\Horizon;

Horizon::routeSmsNotificationsTo('admin@ogunconference.org');
Horizon::routeMailNotificationsTo('admin@ogunconference.org');

return [

    'domain'  => env('HORIZON_DOMAIN'),
    'path'    => env('HORIZON_PATH', 'horizon'),
    'driver'  => env('QUEUE_CONNECTION', 'redis'),
    'memory_limit' => 256, // MB

    'defaults' => [
        'supervisor-1' => [
            'connection'  => 'redis',
            'queue'       => ['critical', 'notifications', 'documents', 'default'],
            'balance'     => 'auto',
            'minProcesses'=> 1,
            'maxProcesses'=> 10,
            'tries'       => 3,
            'timeout'     => 60,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-critical' => [
                'connection'   => 'redis',
                'queue'        => ['critical'],
                'balance'      => 'simple',
                'minProcesses' => 2,
                'maxProcesses' => 5,
                'tries'        => 5,
                'timeout'      => 30,
            ],
            'supervisor-notifications' => [
                'connection'   => 'redis',
                'queue'        => ['notifications'],
                'balance'      => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 5,
                'tries'        => 3,
                'timeout'      => 30,
            ],
            'supervisor-documents' => [
                'connection'   => 'redis',
                'queue'        => ['documents'],
                'balance'      => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 3,
                'tries'        => 3,
                'timeout'      => 90,
            ],
            'supervisor-default' => [
                'connection'   => 'redis',
                'queue'        => ['default'],
                'balance'      => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 5,
                'tries'        => 3,
                'timeout'      => 60,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection'   => 'redis',
                'queue'        => ['critical', 'notifications', 'documents', 'default'],
                'balance'      => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 3,
                'tries'        => 1,
                'timeout'      => 60,
            ],
        ],
    ],

    'metrics' => [
        'trim_snapshots' => [
            'job'  => 24,
            'queue'=> 24,
        ],
    ],

    'waits' => [
        'redis:critical'      => 3,
        'redis:notifications' => 30,
        'redis:documents'     => 120,
        'redis:default'       => 60,
    ],

];
