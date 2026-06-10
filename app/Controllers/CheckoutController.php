<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\CartServiceInterface;
use App\Services\OrderService;
use InvalidArgumentException;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orders,
        private CartServiceInterface $cart
    ) {
        parent::__construct();
    }

    public function store(Request $request): Response
    {
        try {
            $order = $this->orders->checkout([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);
        } catch (InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], 422);
        } catch (RuntimeException $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }

        return $this->json([
            'order' => $order,
            'cart' => $this->cart->state(),
        ], 201);
    }
}
