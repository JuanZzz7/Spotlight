<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mercado Pago México & Colombia Configuration
    |--------------------------------------------------------------------------
    |
    | Documentación oficial:
    | México: https://www.mercadopago.com.mx/developers/es/docs
    | Colombia: https://www.mercadopago.com.co/developers/es/docs
    |
    */

    'mx' => [
        'public_key'     => env('MERCADOPAGO_MX_PUBLIC_KEY', 'TEST-c1d2e3f4-5678-90ab-cdef-1234567890ab'),
        'access_token'   => env('MERCADOPAGO_MX_ACCESS_TOKEN', 'TEST-1234567890123456-102030-abcdef1234567890abcdef1234567890-123456789'),
        'webhook_secret' => env('MERCADOPAGO_MX_WEBHOOK_SECRET', 'test_mp_mx_webhook_secret_123456'),
        'currency'       => 'MXN',
    ],

    'co' => [
        'public_key'     => env('MERCADOPAGO_CO_PUBLIC_KEY', ''),
        'access_token'   => env('MERCADOPAGO_CO_ACCESS_TOKEN', ''),
        'webhook_secret' => env('MERCADOPAGO_CO_WEBHOOK_SECRET', ''),
        'currency'       => 'COP',
    ],

    'sandbox' => (bool) env('MERCADOPAGO_SANDBOX', true),
    'api_base_url' => 'https://api.mercadopago.com',
];
