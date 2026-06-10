<?php

namespace App\Services;

interface FavoritesServiceInterface
{
    public function toggle(string $productId): bool;

    public function ids(): array;

    public function state(): array;
}
