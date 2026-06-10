<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\CartServiceInterface;
use App\Services\FavoritesServiceInterface;
use App\Services\ProductCatalogInterface;

class HomeController extends Controller
{
    public function __construct(
        View $view,
        private ProductCatalogInterface $products,
        private CartServiceInterface $cart,
        private FavoritesServiceInterface $favorites
    ) {
        parent::__construct($view);
    }

    public function index(Request $request): Response
    {
        return $this->html('home.php', [
            'products' => $this->products->all(),
            'cart' => $this->cart->state(),
            'favorites' => $this->favorites->state(),
        ]);
    }
}
