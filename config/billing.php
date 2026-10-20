<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Billing Provider
    |--------------------------------------------------------------------------
    |
    | 'sandbox': Testing adapter simulating exact DIAN 1.9 specifications,
    |            CUFE calculation (SHA-384), UBL 2.1 XML and QR generation.
    | 'factus': Authorized Colombian technology provider REST API integration.
    |
    */
    'default_provider' => env('DIAN_BILLING_PROVIDER', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Global Environment
    |--------------------------------------------------------------------------
    |
    | 'sandbox' (Habilitación / Pruebas DIAN) or 'production'
    |
    */
    'environment' => env('DIAN_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Spotlight Platform Fiscal Defaults (Ubaté, Cundinamarca)
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'municipality_code' => '25843', // Ubaté
        'department_code'   => '25',    // Cundinamarca
        'postal_code'       => '250420',
        'country_code'      => 'CO',
        'country_name'      => 'Colombia',
        'currency'          => 'COP',
        'storage_disk'      => 'local', // Almacenamiento seguro privado (no público)
    ],

    /*
    |--------------------------------------------------------------------------
    | DIAN Validation & Catalog URLs
    |--------------------------------------------------------------------------
    */
    'dian' => [
        'qr_base_url' => env('DIAN_QR_BASE_URL', 'https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey='),
        'sandbox_url' => 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc',
        'prod_url'    => 'https://vpfe.dian.gov.co/WcfDianCustomerServices.svc',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorized Providers Configuration
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'sandbox' => [
            'name' => 'Spotlight DIAN Sandbox Provider (Anexo Técnico 1.9)',
            'driver' => \App\Services\Billing\Providers\SandboxBillingProvider::class,
        ],
        'factus' => [
            'name' => 'Factus API Colombia',
            'driver' => \App\Services\Billing\Providers\FactusBillingProvider::class,
            'sandbox_base_url' => env('FACTUS_SANDBOX_URL', 'https://api-sandbox.factus.co'),
            'prod_base_url'    => env('FACTUS_PROD_URL', 'https://api.factus.co'),
            'client_id'        => env('FACTUS_CLIENT_ID', ''),
            'client_secret'    => env('FACTUS_CLIENT_SECRET', ''),
        ],
    ],
];
