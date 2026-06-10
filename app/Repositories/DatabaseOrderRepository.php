<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class DatabaseOrderRepository
{
    public function __construct(private Database $database)
    {
    }

    public function create(array $cart, string $sessionId, array $customer = []): array
    {
        $connection = $this->database->connection();
        $items = $cart['items'] ?? [];

        $connection->beginTransaction();

        try {
            $this->insertOrder($connection, $cart, $sessionId, $customer);
            $orderId = (int) $connection->lastInsertId();

            foreach ($items as $item) {
                $this->insertOrderItem($connection, $orderId, $item);
            }

            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw $exception;
        }

        return [
            'id' => $orderId,
            'status' => 'new',
            'storage' => 'database',
            'itemsCount' => (int) ($cart['count'] ?? 0),
            'total' => (float) ($cart['total'] ?? 0),
        ];
    }

    private function insertOrder(PDO $connection, array $cart, string $sessionId, array $customer): void
    {
        $statement = $connection->prepare(
            'INSERT INTO orders
                (session_id, status, customer_name, customer_email, currency, items_count, total_amount)
             VALUES
                (:session_id, :status, :customer_name, :customer_email, :currency, :items_count, :total_amount)'
        );

        $statement->execute([
            'session_id' => $sessionId,
            'status' => 'new',
            'customer_name' => $customer['name'] ?? null,
            'customer_email' => $customer['email'] ?? null,
            'currency' => 'USD',
            'items_count' => (int) ($cart['count'] ?? 0),
            'total_amount' => (float) ($cart['total'] ?? 0),
        ]);
    }

    private function insertOrderItem(PDO $connection, int $orderId, array $item): void
    {
        $statement = $connection->prepare(
            'INSERT INTO order_items
                (order_id, product_id, product_name, product_description, unit_price, quantity, line_total)
             VALUES
                (:order_id, :product_id, :product_name, :product_description, :unit_price, :quantity, :line_total)'
        );

        $statement->execute([
            'order_id' => $orderId,
            'product_id' => $item['id'],
            'product_name' => $item['name'],
            'product_description' => $item['description'] ?? null,
            'unit_price' => (float) $item['price'],
            'quantity' => (int) $item['quantity'],
            'line_total' => (float) $item['lineTotal'],
        ]);
    }
}
