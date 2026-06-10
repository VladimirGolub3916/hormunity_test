<?php

namespace App\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    /**
     * @return Product[]
     */
    public function all(): array;

    public function find(string $id): ?Product;
}
