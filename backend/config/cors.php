<?php

return [
    'paths'                    => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods'          => ['*'],
    'allowed_origins'          => array_values(array_filter(array_map('trim', explode(',', env('FRONTEND_URL', 'http://localhost:5173') . ',' . env('RENTAL_FRONTEND_URL', ''))))),
    // Support Vercel preview deployments (*.vercel.app) dan domain custom klien
    'allowed_origins_patterns' => array_values(array_filter(array_map('trim', explode(',', env('CORS_ORIGIN_PATTERNS', ''))))),
    'allowed_headers'          => ['*'],
    'exposed_headers'          => [],
    'max_age'                  => 0,
    'supports_credentials'     => true,
];
