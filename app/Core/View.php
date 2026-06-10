<?php

namespace App\Core;

use RuntimeException;

class View
{
    public function __construct(private string $basePath)
    {
    }

    public function render(string $template, array $data = []): string
    {
        $file = $this->basePath . '/' . ltrim($template, '/');

        if (!is_file($file)) {
            throw new RuntimeException("View {$template} was not found.");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $file;

        return ob_get_clean();
    }

    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public function url(string $path = ''): string
    {
        return Url::to($path);
    }

    public function basePath(): string
    {
        return Url::basePath();
    }

    public function money(float $value): string
    {
        return '$' . number_format($value, 2);
    }
}
