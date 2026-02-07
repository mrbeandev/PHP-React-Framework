<?php

use App\Core\Support\Env;

return [
    'connection' => Env::get('DB_CONNECTION', 'sqlite'),
    'prefix' => Env::get('DB_PREFIX', ''),
    'sqlite' => [
        'database' => Env::get('DB_DATABASE', 'database/database.sqlite'),
    ],
    'mysql' => [
        'host' => Env::get('DB_HOST', '127.0.0.1'),
        'port' => Env::get('DB_PORT', '3306'),
        'database' => Env::get('DB_DATABASE', ''),
        'username' => Env::get('DB_USERNAME', ''),
        'password' => Env::get('DB_PASSWORD', ''),
        'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
        'collation' => Env::get('DB_COLLATION', 'utf8mb4_unicode_ci'),
    ],
];
