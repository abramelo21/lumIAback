<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'up'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_filter(explode(',', (string) env('FRONTEND_URL', 'http://localhost:5173'))),
    'allowed_origins_patterns' => [
        '#^https://[a-z0-9.-]+\.pages\.dev$#',
        '#^https://[a-z0-9-]+\.trycloudflare\.com$#',
        '#^https://[a-z0-9-]+\.ngrok-free\.dev$#',
        '#^https://[a-z0-9-]+\.ngrok-free\.app$#',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
