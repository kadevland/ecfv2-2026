<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [
    'name'   => env('HORIZON_NAME'),
    'domain' => env('HORIZON_DOMAIN'),
    'path'   => env('HORIZON_PATH', 'horizon'),
    'use'    => 'default',
    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_') . '_horizon:'
    ),
    'middleware' => ['web'],
    'waits'      => [
        'redis:default'      => 60,        //  Production plus conservateur
        'redis:mongodb-sync' => 30,   //  MongoDB sync
    ],

    'trim' => [
        'recent'        => 60,
        'pending'       => 60,
        'completed'     => 60,
        'recent_failed' => 10080,
        'failed'        => 10080,
        'monitored'     => 10080,
    ],

    'silenced'       => [],
    'silenced_tags'  => [],
    'allowed_emails' => array_filter(explode(',', env('HORIZON_ALLOWED_EMAILS', ''))),

    'metrics' => [
        'trim_snapshots' => [
            'job'   => 24,
            'queue' => 24,
        ],
    ],

    'fast_termination' => true,


    'memory_limit' => 512,



    'defaults' => [
        //  Supervisor principal : Équilibré production
        'supervisor-default' => [
            'connection'          => 'redis',
            'queue'               => ['default'],
            'balance'             => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses'        => 6,      //  6 workers (4 vCPU * 1.5)
            'minProcesses'        => 2,      //  Minimum production
            'maxTime'             => 3600,   //  1h max
            'maxJobs'             => 1000,   //  Jobs avant restart
            'memory'              => 256,    //  256MB par worker
            'tries'               => 3,
            'timeout'             => 120,
            'nice'                => 0,
            'balanceMaxShift'     => 1,      //  Scaling modéré
            'balanceCooldown'     => 3,      //  Cooldown normal
        ],

        // Supervisor MongoDB : Optimisé VPS
        'supervisor-mongodb' => [
            'connection'          => 'redis',
            'queue'               => ['mongodb-sync'],
            'balance'             => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses'        => 8,      //  8 workers MongoDB
            'minProcesses'        => 2,      //  2 workers minimum
            'maxTime'             => 1800,
            'maxJobs'             => 500,
            'memory'              => 192,    //  192MB par worker
            'tries'               => 5,
            'timeout'             => 90,
            'nice'                => 0,
            'balanceMaxShift'     => 2,
            'balanceCooldown'     => 2,
        ],

        // Supervisor événements : Rapide et léger
        'supervisor-events' => [
            'connection'          => 'redis',
            'queue'               => ['events', 'high'],
            'balance'             => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses'        => 4,      //  4 workers events
            'minProcesses'        => 1,
            'maxTime'             => 1800,
            'maxJobs'             => 200,
            'memory'              => 128,    //  128MB par worker
            'tries'               => 2,
            'timeout'             => 30,
            'nice'                => 0,
            'balanceMaxShift'     => 1,
            'balanceCooldown'     => 2,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-default' => [
                'maxProcesses'    => 6,      //  PROD VPS = 6 workers
                'minProcesses'    => 2,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
            ],
            'supervisor-mongodb' => [
                'maxProcesses'    => 8,      //  PROD VPS = 8 workers MongoDB
                'minProcesses'    => 2,
                'balanceMaxShift' => 2,
                'balanceCooldown' => 2,
            ],
            'supervisor-events' => [
                'maxProcesses'    => 4,      //  PROD VPS = 4 workers events
                'minProcesses'    => 1,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 2,
            ],
        ],

        'local' => [
            // Identique pour tests locaux sur VPS
            'supervisor-default' => [
                'maxProcesses'    => 4,
                'minProcesses'    => 1,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
            ],
            'supervisor-mongodb' => [
                'maxProcesses'    => 6,
                'minProcesses'    => 1,
                'balanceMaxShift' => 2,
                'balanceCooldown' => 3,
            ],
            'supervisor-events' => [
                'maxProcesses'    => 3,
                'minProcesses'    => 1,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
            ],
        ],
    ],
];
