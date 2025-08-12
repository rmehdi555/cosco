<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for IPPanel SMS service
    |
    */

    'ippanel' => [
        'url' => env('SMS_IPPANEL_URL', 'https://ippanel.com/services.jspd'),
        'username' => env('SMS_IPPANEL_USERNAME', 'co09121021818'),
        'password' => env('SMS_IPPANEL_PASSWORD', 'Faraz@1753471192'),
        'from' => env('SMS_IPPANEL_FROM', '983000505'),
        'timeout' => env('SMS_IPPANEL_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default SMS Provider
    |--------------------------------------------------------------------------
    |
    | Which SMS provider to use by default
    |
    */

    'default' => env('SMS_DEFAULT_PROVIDER', 'ippanel'),

    /*
    |--------------------------------------------------------------------------
    | SMS Logging
    |--------------------------------------------------------------------------
    |
    | Whether to log SMS sending attempts
    |
    */

    'log_attempts' => env('SMS_LOG_ATTEMPTS', true),
];
