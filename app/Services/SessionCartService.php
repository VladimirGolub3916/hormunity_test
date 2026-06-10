<?php

namespace App\Services;

use InvalidArgumentException;

class SessionCartService implements CartServiceInterface
{
    private const KEY = 'hormunity_cart';

    public function __construct(
        private SessionStoreInterface $session,
        private ProductCatalogInterface $catalog
    ) {
    }

    public function add(string $productId): void
    {
        $this->assertProductExists($productId);

        $items = $this->items();
        $items[$productId] = ($items[$productId] ?? 0) + 1;

        $this->session->set(self::KEY, $items);
    }

    public function remove(string $productId): void
    {
        $items = $this->items();

        if (!isset($items[$productId])) {
            return;
        }

        $items[$productId]--;

        if ($items[$productId] <= 0) {
            unset($items[$productId]);
        }

        $this->session->set(self::KEY, $items);
    }

    public function clear(): void
    {
        $this->session->remove(self::KEY);
    }

    public function state(): array
    {
        $items = [];
        $count = 0;
        $total = 0.0;

        foreach ($this->items() as $productId => $quantity) {
            $product = $this->catalog->find((string) $productId);

            if ($product === null) {
                continue;
            }

            $quantity = max(1, (int) $quantity);
            $lineTotal = $product->price() * $quantity;
            $count += $quantity;
            $total += $lineTotal;

            $items[] = [
                'id' => $product->id(),
                'name' => $product->name(),
                'description' => $product->description(),
                'price' => $product->price(),
                'quantity' => $quantity,
                'lineTotal' => $lineTotal,
            ];
        }

        return [
            'items' => $items,
            'count' => $count,
            'total' => $total,
        ];
    }

    private function items(): array
    {
        $items = $this->session->get(self::KEY, []);

        return is_array($items) ? $items : [];
    }

    private function assertProductExists(string $productId): void
    {
        if ($this->catalog->find($productId) === null) {
            throw new InvalidArgumentException('Product was not found.');
        }
    }
}
