<?php

$root = dirname(__DIR__);

require $root . '/app/Core/Autoloader.php';

App\Core\Autoloader::register($root . '/app');
App\Core\Env::load($root);

$config = require $root . '/config/database.php';
$migrationPath = __DIR__ . '/migrations';

$dsn = sprintf(
    'mysql:host=%s;port=%d;charset=%s',
    $config['host'],
    $config['port'],
    $config['charset']
);

$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

$files = glob($migrationPath . '/*.sql') ?: [];
sort($files);

foreach ($files as $file) {
    $pdo->exec(file_get_contents($file));
    echo 'Migrated: ' . basename($file) . PHP_EOL;
}
