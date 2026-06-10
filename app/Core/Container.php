<?php

namespace App\Core;

use RuntimeException;

class Container
{
    private array $definitions = [];
    private array $instances = [];

    public function set(string $id, callable $factory): void
    {
        $this->definitions[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (!array_key_exists($id, $this->definitions)) {
            throw new RuntimeException("Service {$id} is not registered.");
        }

        $this->instances[$id] = $this->definitions[$id]($this);

        return $this->instances[$id];
    }
}
