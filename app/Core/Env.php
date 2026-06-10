<?php

namespace App\Core;

class Env
{
    private static array $values = [];
    private static bool $loaded = false;

    public static function load(string $rootPath): void
    {
        if (self::$loaded) {
            return;
        }

        $file = rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.env';

        if (!is_readable($file)) {
            self::$loaded = true;

            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            self::$values[trim($key)] = trim(trim($value), "\"'");
        }

        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, self::$values)) {
            return self::$values[$key];
        }

        $value = getenv($key);

        return $value === false ? $default : $value;
    }
}
