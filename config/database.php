<?php

use App\Core\Env;

return [
    'host' => Env::get('DB_HOST', '127.0.0.1'),
    'port' => (int) Env::get('DB_PORT', '3306'),
    'dbname' => Env::get('DB_DATABASE', 'hormunity_db'),
    'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
    'username' => Env::get('DB_USERNAME', 'root'),
    'password' => Env::get('DB_PASSWORD', ''),
];
