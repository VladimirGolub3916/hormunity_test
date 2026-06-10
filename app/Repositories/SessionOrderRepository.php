<?php

namespace App\Repositories;

use App\Services\SessionStoreInterface;

class SessionOrderRepository
{
    private const KEY = 'hormunity_orders';

    public function __construct(private SessionStoreInterface $session)
    {
    }

    public function create(array $cart, string $sessionId, array $customer = []): array
    {
        $orders = $this->orders();
        $orderId = 'session-' . (count($orders) + 1);
        $order = [
            'id' => $orderId,
            'status' => 'new',
            'storage' => 'session',
            'sessionId' => $sessionId,
            'customer' => [
                'name' => $customer['name'] ?? null,
                'email' => $customer['email'] ?? null,
            ],
            'items' => $cart['items'] ?? [],
            'itemsCount' => (int) ($cart['count'] ?? 0),
            'total' => (float) ($cart['total'] ?? 0),
            'createdAt' => date('c'),
        ];

        $orders[] = $order;
        $this->session->set(self::KEY, $orders);

        return [
            'id' => $orderId,
            'status' => 'new',
            'storage' => 'session',
            'itemsCount' => $order['itemsCount'],
            'total' => $order['total'],
        ];
    }

    private function orders(): array
    {
        $orders = $this->session->get(self::KEY, []);

        return is_array($orders) ? $orders : [];
    }
}
