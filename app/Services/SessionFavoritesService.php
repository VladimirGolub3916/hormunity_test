<?php

namespace App\Services;

use InvalidArgumentException;

class SessionFavoritesService implements FavoritesServiceInterface
{
    private const KEY = 'hormunity_favorites';

    public function __construct(
        private SessionStoreInterface $session,
        private ProductCatalogInterface $catalog
    ) {
    }

    public function toggle(string $productId): bool
    {
        $this->assertProductExists($productId);

        $ids = $this->ids();

        if (in_array($productId, $ids, true)) {
            $ids = array_values(array_filter($ids, fn (string $id) => $id !== $productId));
            $this->session->set(self::KEY, $ids);

            return false;
        }

        $ids[] = $productId;
        $this->session->set(self::KEY, array_values(array_unique($ids)));

        return true;
    }

    public function ids(): array
    {
        $ids = $this->session->get(self::KEY, []);

        if (!is_array($ids)) {
            return [];
        }

        return array_values(array_filter($ids, fn (mixed $id) => is_string($id) && $this->catalog->find($id) !== null));
    }

    public function state(): array
    {
        $ids = $this->ids();

        return [
            'ids' => $ids,
            'count' => count($ids),
        ];
    }

    private function assertProductExists(string $productId): void
    {
        if ($this->catalog->find($productId) === null) {
            throw new InvalidArgumentException('Product was not found.');
        }
    }
}
