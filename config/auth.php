<?php

return [

    'defaults' => [
        'guard'   => 'web',       // guard default untuk Pengguna
        'passwords' => 'penggunas',
    ],

    'guards' => [
        // guard default (Pengguna)
        'web' => [
            'driver'   => 'session',
            'provider' => 'penggunas',
        ],

        // guard terpisah untuk Admin
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'penggunas' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Pengguna::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'penggunas' => [
            'provider' => 'penggunas',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
        'admins' => [
            'provider' => 'admins',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
