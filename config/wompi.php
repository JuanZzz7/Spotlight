<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Wompi Colombia Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Documentación oficial: https://docs.wompi.co/docs/colombia/widget-checkout-web/
    |
    */

    'public_key'        => env('WOMPI_PUBLIC_KEY', 'pub_test_Q5yDA9xoKdePzhSGeVe9KStPUUO0edeN'),
    'private_key'       => env('WOMPI_PRIVATE_KEY', 'prv_test_X2v0oE2O0o5hUe2hVj5bW0e7Z0e9X8u7'),
    'integrity_secret'  => env('WOMPI_INTEGRITY_SECRET', 'test_integrity_b8a1c9d2e3f4a5b6c7d8e9f0a1b2c3d4'),
    'event_secret'      => env('WOMPI_EVENT_SECRET', 'test_events_d9e8f7a6b5c4d3e2f1a0b9c8d7e6f5a4'),
    'sandbox'           => env('WOMPI_SANDBOX', true),
    'currency'          => env('WOMPI_CURRENCY', 'COP'),
    'webhook_url'       => env('WOMPI_WEBHOOK_URL', env('APP_URL', 'http://localhost') . '/api/payments/wompi/webhook'),

    /*
    |--------------------------------------------------------------------------
    | Parámetros Comerciales del Marketplace Spotlight
    |--------------------------------------------------------------------------
    */
    'platform_fee_percent'    => (float) env('MARKETPLACE_PLATFORM_FEE_PERCENT', 5.0), // 5% comisión base
    'shipping_cost_per_vendor'=> (float) env('MARKETPLACE_SHIPPING_COST_PER_VENDOR', 6000), // $6.000 COP por comercio
    'free_shipping_threshold' => (float) env('MARKETPLACE_FREE_SHIPPING_THRESHOLD', 120000), // Envío gratis a partir de $120.000

    /*
    |--------------------------------------------------------------------------
    | URLs Wompi
    |--------------------------------------------------------------------------
    */
    'api_base_url'      => env('WOMPI_SANDBOX', true)
        ? 'https://sandbox.wompi.co/v1'
        : 'https://production.wompi.co/v1',
    'widget_js_url'     => 'https://checkout.wompi.co/widget.js',
];
