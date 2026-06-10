<?php

use App\Core\Request;

if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $file = __DIR__ . $path;

    if ($path !== '/' && is_file($file)) {
        return false;
    }
}

[$container, $router] = require dirname(__DIR__) . '/bootstrap.php';

$response = $router->dispatch(Request::fromGlobals(), $container);
$response->send();
