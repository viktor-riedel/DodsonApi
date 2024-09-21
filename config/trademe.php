<?php

return [
    'secret' => env('TRADEME_SECRET'),
    'key' => env('TRADEME_KEY'),
    'environment' => env('TRADEME_ENVIRONMENT', 'sandbox'),
    'sandbox_urls' => [
        'api_url' => env('TRADEME_API_POINT_SANDBOX'),
        'auth_url' => env('TRADEME_ENVIRONMENT_SANDBOX_AUTH_URL'),
        'verifier_url' => env('TRADEME_ENVIRONMENT_SANDBOX_AUTH_VERIFIER_URL'),
        'auth_access_token_url' => env('TRADEME_ENVIRONMENT_SANDBOX_AUTH_ACCESS_TOKEN_URL',)
    ],
    'production_urls' => [
        'api_url' => env('TRADEME_API_POINT_PROD'),
        'auth_url' => env('TRADEME_ENVIRONMENT_PROD_AUTH_URL'),
        'verifier_url' => env('TRADEME_ENVIRONMENT_PROD_AUTH_VERIFIER_URL'),
        'auth_access_token_url' => env('TRADEME_ENVIRONMENT_PROD_AUTH_ACCESS_TOKEN_URL',)
    ]
];
