<?php

namespace App\Core;

final class Autoloader
{
    private const PREFIX = 'App\\';

    public static function register(string $basePath): void
    {
        spl_autoload_register(static function (string $class) use ($basePath): void {
            $prefixLength = strlen(self::PREFIX);

            if (strncmp(self::PREFIX, $class, $prefixLength) !== 0) {
                return;
            }

            $relativeClass = substr($class, $prefixLength);
            $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
            $file = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relativePath;

            if (is_file($file)) {
                require $file;
            }
        });
    }
}
