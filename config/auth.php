<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'api_User'),
    ],

    'guards' => [
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'api_User',
            'hash' => false,
        ],
    ],

    'providers' => [
        'api_User' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\apiUser::class),
        ],
    ], 

    'passwords' => [
        'api_User' => [ 
            'provider' => 'api_User',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

]; 