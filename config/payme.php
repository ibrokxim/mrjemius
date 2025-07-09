<?php

return [
    'test_token' => env('PAYME_TEST_PROVIDER_TOKEN'),
    'kassa_key_for_callback' => env('PAYME_KEY'),
    'min_amount' => env('PAYME_MIN_AMOUNT', 1_000_0),
    'max_amount' => env('PAYME_MAX_AMOUNT', 100_000_000_00),
    'identity' => env('PAYME_IDENTITY', 'id'),
    'login' => 'Paycom',
    'key' => env('PAYME_KEY', 'TestKey'),
    'merchant_id' => env('PAYME_MERCHANT_ID'),
    'allowed_ips' => [
        "127.0.0.1",
        "185.234.113.1",
        "185.234.113.2",
        "185.234.113.3",
        "185.234.113.4",
        "185.234.113.5",
        "185.234.113.6",
        "185.234.113.7",
        "185.234.113.8",
        '185.234.113.9',
        '185.234.113.10',
        '185.234.113.11',
        '185.234.113.12',
        '185.234.113.13',
        '185.234.113.14',
        '185.234.113.15',
    ]
];
