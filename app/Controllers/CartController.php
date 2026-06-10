<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\CartServiceInterface;
use InvalidArgumentException;

class CartController extends Controller
{
    public function __construct(private CartServiceInterface $cart)
    {
        parent::__construct();
    }

    public function show(Request $request): Response
    {
        return $this->json(['cart' => $this->cart->state()]);
    }

    public function add(Request $request): Response
    {
        $productId = (string) $request->input('productId', '');

        try {
            $this->cart->add($productId);
        } catch (InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], 422);
        }

        return $this->json(['cart' => $this->cart->state()]);
    }

    public function remove(Request $request): Response
    {
        $this->cart->remove((string) $request->input('productId', ''));

        return $this->json(['cart' => $this->cart->state()]);
    }

    public function clear(Request $request): Response
    {
        $this->cart->clear();

        return $this->json(['cart' => $this->cart->state()]);
    }
}
