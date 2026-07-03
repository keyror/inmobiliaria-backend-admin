<?php

$appUrl = env('APP_URL', 'https://inmobiliaria.com');
$appDomain = env('APP_DOMAIN', parse_url($appUrl, PHP_URL_HOST) ?: 'inmobiliaria.com');

$allowedOrigins = array_filter(array_map(
    'trim',
    explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://127.0.0.1:3000'))
));

$allowedOrigins[] = $appUrl;
$allowedOrigins = array_values(array_unique($allowedOrigins));

$allowedOriginsPatterns = [
    sprintf('#^https://([a-z0-9-]+\.)*%s$#', preg_quote($appDomain, '#')),
];

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => $allowedOriginsPatterns,

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
