<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function dispatch(Request $request, Container $container): Response
    {
        $handler = $this->routes[$request->method()][$request->path()] ?? null;

        if ($handler === null) {
            return new Response('Not found', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        [$controllerClass, $method] = $handler;
        $controller = $container->get($controllerClass);

        return $controller->{$method}($request);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[$method][rtrim($path, '/') ?: '/'] = $handler;
    }
}
