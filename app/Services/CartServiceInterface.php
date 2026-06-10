<?php

namespace App\Services;

interface CartServiceInterface
{
    public function add(string $productId): void;

    public function remove(string $productId): void;

    public function clear(): void;

    public function state(): array;
}
