<?php

class OrderItem {

    use ItemOperations;

    private int $id;

    private PDO $pdo;

    private Order $order;

    public function __construct() {

        try {

            $this->pdo = new PDO('pgsql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function getId(): int {

        return $this->id;
    }

    public function getAllOrderItems(int $order_id): ?array {

        try {

            $stmt = $this->pdo->prepare('SELECT oi.id as id, "item".id as item_id, "item".name as name,
                                               oi.price as price, oi.quantity as quantity, "order".date as date
                                               FROM order_item as oi
                                               JOIN "item" ON "item".id = oi.item_id
                                                JOIN "order" ON "order".id = oi.order_id
                                                WHERE "order".id = ?');
            $stmt->execute([$order_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function createOrderItem($order_id, $item_id, $quantity) {

        try {

            // проверка наличия товара в БД

            if ($this->isExist($this->pdo, $item_id)) {

                $itemPrice = $this->getPrice($this->pdo, $item_id());     // получение цены товара

                // проверка наличия товара в корзине

                if ($this->checkCart($this->pdo, $this->order->getCart()->getId(), $item_id)) {

                    throw new ServerException("Ошибка при работе с БД: такой товар уже есть в чеке.");
                }
                else {

                    // в случае отсутствия - добавление новой позиции в чек

                    $stmt = $this->pdo->prepare('INSERT INTO order_item (item_id, order_id, quantity, price)
                                                        VALUES (?, ?, ?, ?) RETURNING id');

                    $stmt->execute([$item_id, $order_id, $quantity, $itemPrice]);
                    $this->id = $stmt->fetchColumn();

                    return "Новая позиция в чек добавлена.";
                }
            }
            else {

                throw new ServerException("Ошибка при работе с БД: такой товар не найден.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function showOrderItem(int $id): ?array {

        try {

            $stmt = $this->pdo->prepare('SELECT "item".id as item_id, "item".name as name, oi.price as price,
                                                oi.quantity as quantity, "order".id as order_id, "order".date as date
                                               FROM order_item as oi
                                               JOIN "item" ON "item".id = oi.item_id
                                                JOIN "order" ON "order".id = oi.order_id
                                                WHERE oi.id = ?');

            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function updateOrderItem($order_id, $item_id, $quantity) {

        try {

            // проверка наличия товара в БД

            if ($this->isExist($this->pdo, $item_id)) {

                $itemPrice = $this->getPrice($this->pdo, $item_id);     // получение цены товара из БД

                // проверка наличия товара в корзине

                if ($itemInCart = $this->checkCart($this->pdo, $this->order->getCart()->getId(), $item_id)) {

                    $stmt = $this->pdo->prepare('UPDATE order_item SET quantity = ?, price = ?
                                                        WHERE order_id = ? AND item_id = ?');
                    $stmt->execute([$quantity, $itemPrice, $order_id, $item_id]);

                    return "Данные о позиции в чеке изменены.";
                }
                else {

                    throw new ServerException("Ошибка при работе с БД: такой товар в корзине не найден.");
                }
            }
            else {

                throw new ServerException("Ошибка при работе с БД: такой товар не найден.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function deleteOrderItem(int $id) {

        try {

            $stmt = $this->pdo->prepare('DELETE FROM order_item WHERE id = ?');
            $stmt->execute([$id]);

            return "Позиция из чека успешно удалена.";
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
