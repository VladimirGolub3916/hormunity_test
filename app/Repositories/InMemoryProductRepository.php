<?php

namespace App\Repositories;

use App\Models\Product;

class InMemoryProductRepository implements ProductRepositoryInterface
{
    private array $products = [];

    public function __construct(array $items)
    {
        foreach ($items as $item) {
            $product = new Product(
                $item['id'],
                $item['name'],
                $item['description'],
                (float) $item['price'],
                isset($item['old_price']) ? (float) $item['old_price'] : null,
                $item['image'],
                $item['alt'],
                $item['tag_label'] ?? null,
                $item['tag_class'] ?? null,
                (bool) ($item['featured'] ?? false),
                (bool) ($item['muted'] ?? false)
            );

            $this->products[$product->id()] = $product;
        }
    }

    public function all(): array
    {
        return array_values($this->products);
    }

    public function find(string $id): ?Product
    {
        return $this->products[$id] ?? null;
    }
}
