<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;

class ProductCatalog implements ProductCatalogInterface
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function all(): array
    {
        return $this->products->all();
    }

    public function find(string $id): ?Product
    {
        return $this->products->find($id);
    }
}
