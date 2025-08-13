<?php

return [
    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'melli_test'),
    
    'gateways' => [
        'melli_test' => [
            'name' => 'بانک ملی تستی',
            'type' => 'melli_test',
            'enabled' => env('MELLI_TEST_ENABLED', true),
            'merchant_id' => env('MELLI_TEST_MERCHANT_ID', '12345'),
            'terminal_id' => env('MELLI_TEST_TERMINAL_ID', 'GBHDTY98'),
            'key' => env('MELLI_TEST_KEY', 'MzVlOTU1NmVhYWM1MDrhOWFlYTVjMDJi'),
            'request_url' => 'https://sandbox.banktest.ir/melli/sadad.shaparak.ir/VPG/api/v0/Request/PaymentRequest',
            'verify_url' => 'https://sandbox.banktest.ir/melli/sadad.shaparak.ir/VPG/api/v0/Advice/Verify',
            'gateway_url' => 'https://sandbox.banktest.ir/melli/sadad.shaparak.ir/VPG/Purchase',
        ],
        
        'zarinpal_test' => [
            'name' => 'زرین‌پال تستی',
            'type' => 'zarinpal_test',
            'enabled' => env('ZARINPAL_TEST_ENABLED', true),
            'merchant_id' => env('ZARINPAL_TEST_MERCHANT_ID', 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'),
            'request_url' => 'https://sandbox.zarinpal.com/pg/v4/payment/request.json',
            'verify_url' => 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json',
            'gateway_url' => 'https://sandbox.zarinpal.com/pg/StartPay/',
        ],
        
        'zarinpal' => [
            'name' => 'زرین‌پال',
            'type' => 'zarinpal',
            'enabled' => env('ZARINPAL_ENABLED', false),
            'merchant_id' => env('ZARINPAL_MERCHANT_ID', ''),
            'request_url' => 'https://api.zarinpal.com/pg/v4/payment/request.json',
            'verify_url' => 'https://api.zarinpal.com/pg/v4/payment/verify.json',
            'gateway_url' => 'https://www.zarinpal.com/pg/StartPay/',
        ],
        
        'asanpardakht' => [
            'name' => 'آسان پرداخت',
            'type' => 'asanpardakht',
            'enabled' => env('ASANPARDAKHT_ENABLED', false),
            'merchant_id' => env('ASANPARDAKHT_MERCHANT_ID', '123456789'),
            'callback_url' => env('ASANPARDAKHT_CALLBACK_URL', 'http://localhost:8000/payment/callback'),
            'api_url' => env('ASANPARDAKHT_API_URL', 'https://pay.asanpardakht.ir/'),
            'test_mode' => env('ASANPARDAKHT_TEST_MODE', true),
        ],
    ],
];
