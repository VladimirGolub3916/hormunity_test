<?php

namespace App\Services;

use App\Models\Product;

interface ProductCatalogInterface
{
    /**
     * @return Product[]
     */
    public function all(): array;

    public function find(string $id): ?Product;
}
