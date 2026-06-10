<?php

use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\FavoritesController;
use App\Controllers\HomeController;
use App\Core\Database;
use App\Core\Container;
use App\Core\Env;
use App\Core\Router;
use App\Core\View;
use App\Repositories\DatabaseOrderRepository;
use App\Repositories\InMemoryProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\SessionOrderRepository;
use App\Services\CartServiceInterface;
use App\Services\FavoritesServiceInterface;
use App\Services\OrderService;
use App\Services\PhpSessionStore;
use App\Services\ProductCatalog;
use App\Services\ProductCatalogInterface;
use App\Services\SessionCartService;
use App\Services\SessionFavoritesService;
use App\Services\SessionStoreInterface;

define('ROOT_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEW_PATH', ROOT_PATH . '/views');

require ROOT_PATH . '/app/Core/Autoloader.php';

App\Core\Autoloader::register(ROOT_PATH . '/app');
Env::load(ROOT_PATH);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$container = new Container();

$container->set(Database::class, fn () => new Database(require CONFIG_PATH . '/database.php'));
$container->set(View::class, fn () => new View(VIEW_PATH));
$container->set(SessionStoreInterface::class, fn () => new PhpSessionStore());
$container->set(ProductRepositoryInterface::class, fn () => new InMemoryProductRepository(require CONFIG_PATH . '/products.php'));
$container->set(DatabaseOrderRepository::class, fn (Container $container) => new DatabaseOrderRepository(
    $container->get(Database::class)
));
$container->set(SessionOrderRepository::class, fn (Container $container) => new SessionOrderRepository(
    $container->get(SessionStoreInterface::class)
));
$container->set(OrderService::class, fn (Container $container) => new OrderService(
    $container->get(CartServiceInterface::class),
    fn () => $container->get(DatabaseOrderRepository::class),
    $container->get(SessionOrderRepository::class)
));
$container->set(ProductCatalogInterface::class, fn (Container $container) => new ProductCatalog(
    $container->get(ProductRepositoryInterface::class)
));
$container->set(CartServiceInterface::class, fn (Container $container) => new SessionCartService(
    $container->get(SessionStoreInterface::class),
    $container->get(ProductCatalogInterface::class)
));
$container->set(FavoritesServiceInterface::class, fn (Container $container) => new SessionFavoritesService(
    $container->get(SessionStoreInterface::class),
    $container->get(ProductCatalogInterface::class)
));
$container->set(HomeController::class, fn (Container $container) => new HomeController(
    $container->get(View::class),
    $container->get(ProductCatalogInterface::class),
    $container->get(CartServiceInterface::class),
    $container->get(FavoritesServiceInterface::class)
));
$container->set(CartController::class, fn (Container $container) => new CartController(
    $container->get(CartServiceInterface::class)
));
$container->set(CheckoutController::class, fn (Container $container) => new CheckoutController(
    $container->get(OrderService::class),
    $container->get(CartServiceInterface::class)
));
$container->set(FavoritesController::class, fn (Container $container) => new FavoritesController(
    $container->get(FavoritesServiceInterface::class)
));

$router = new Router();
(require ROOT_PATH . '/routes/web.php')($router);

return [$container, $router];
