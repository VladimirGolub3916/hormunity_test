<?php

use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\FavoritesController;
use App\Controllers\HomeController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/cart', [CartController::class, 'show']);
    $router->post('/cart/add', [CartController::class, 'add']);
    $router->post('/cart/remove', [CartController::class, 'remove']);
    $router->post('/cart/clear', [CartController::class, 'clear']);
    $router->post('/checkout', [CheckoutController::class, 'store']);
    $router->get('/favorites', [FavoritesController::class, 'show']);
    $router->post('/favorites/toggle', [FavoritesController::class, 'toggle']);
};
