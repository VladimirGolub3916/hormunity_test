<?php

namespace App\Core;

class Url
{
    public static function to(string $path = ''): string
    {
        if ($path === '' || $path === '/') {
            $basePath = self::basePath();

            return $basePath === '' ? '/' : $basePath . '/';
        }

        if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $path) || str_starts_with($path, '#')) {
            return $path;
        }

        return rtrim(self::basePath(), '/') . '/' . ltrim($path, '/');
    }

    public static function routePath(string $requestPath): string
    {
        $path = '/' . ltrim($requestPath, '/');
        $basePath = self::basePath();

        if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        return rtrim($path, '/') ?: '/';
    }

    public static function basePath(): string
    {
        $configured = Env::get('APP_BASE_PATH');

        if ($configured !== null && trim($configured) !== '') {
            return self::normalizeBasePath($configured);
        }

        return self::detectBasePath();
    }

    private static function detectBasePath(): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

        if ($scriptName === '') {
            return '';
        }

        if (str_ends_with($scriptName, '/public/index.php')) {
            return self::normalizeBasePath(substr($scriptName, 0, -strlen('/public/index.php')));
        }

        if (str_ends_with($scriptName, '/index.php')) {
            return self::normalizeBasePath(substr($scriptName, 0, -strlen('/index.php')));
        }

        return self::normalizeBasePath(dirname($scriptName));
    }

    private static function normalizeBasePath(string $basePath): string
    {
        $basePath = trim(str_replace('\\', '/', $basePath));

        if ($basePath === '' || $basePath === '/' || $basePath === '.') {
            return '';
        }

        return '/' . trim($basePath, '/');
    }
}
