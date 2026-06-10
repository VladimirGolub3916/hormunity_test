<?php

namespace App\Core;

class Request
{
    public function __construct(
        private string $method,
        private string $path,
        private array $input = []
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = Url::routePath($path);
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $input = $_POST;

        if (str_contains($contentType, 'application/json')) {
            $decoded = json_decode(file_get_contents('php://input') ?: '{}', true);
            $input = is_array($decoded) ? $decoded : [];
        }

        return new self($method, $path, $input);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->input[$key] ?? $default;
    }
}
