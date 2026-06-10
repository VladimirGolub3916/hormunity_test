<?php

namespace App\Services;

use Closure;
use App\Repositories\DatabaseOrderRepository;
use App\Repositories\SessionOrderRepository;
use InvalidArgumentException;
use Throwable;

class OrderService
{
    public function __construct(
        private CartServiceInterface $cart,
        private Closure $databaseOrders,
        private SessionOrderRepository $sessionOrders
    ) {
    }

    public function checkout(array $customer = []): array
    {
        $cart = $this->cart->state();

        if (($cart['count'] ?? 0) <= 0) {
            throw new InvalidArgumentException('Cart is empty.');
        }

        try {
            $order = ($this->databaseOrders)()->create($cart, session_id(), $customer);
        } catch (Throwable) {
            $order = $this->sessionOrders->create($cart, session_id(), $customer);
        }

        $this->cart->clear();

        return $order;
    }
}
