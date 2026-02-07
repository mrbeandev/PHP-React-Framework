<?php

use App\Core\Support\Env;

return [
    'allow_origin' => Env::get('CORS_ALLOW_ORIGIN', '*'),
    'allow_methods' => Env::get('CORS_ALLOW_METHODS', 'GET, POST, PUT, PATCH, DELETE, OPTIONS'),
    'allow_headers' => Env::get('CORS_ALLOW_HEADERS', 'Content-Type, Authorization, X-Requested-With, X-API-Key'),
];
