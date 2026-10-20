<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],


    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
    ],

    'wompi' => [
        'public_key'       => env('WOMPI_PUBLIC_KEY', 'pub_test_Q5yDA9xoKdePzhSGeVe9KStPUUO0edeN'),
        'private_key'      => env('WOMPI_PRIVATE_KEY', 'prv_test_X2v0oE2O0o5hUe2hVj5bW0e7Z0e9X8u7'),
        'integrity_secret' => env('WOMPI_INTEGRITY_SECRET', 'test_integrity_b8a1c9d2e3f4a5b6c7d8e9f0a1b2c3d4'),
        'event_secret'     => env('WOMPI_EVENT_SECRET', 'test_events_d9e8f7a6b5c4d3e2f1a0b9c8d7e6f5a4'),
        'sandbox'          => env('WOMPI_SANDBOX', true),
        'currency'         => env('WOMPI_CURRENCY', 'COP'),
    ],

];
