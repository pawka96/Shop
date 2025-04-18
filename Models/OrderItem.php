<?php

class OrderItem {

    private int $id;

    private PDO $pdo;

    private Order $order;

    private Item $item;

    public function __construct(Order $order, Item $item) {

        $this->order = $order;
        $this->item = $item;

        try {

            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function getId(): int {

        return $this->id;
    }

    public function isExistItem(): bool {

        // проверка наличия товара

        $stmt = $this->pdo->prepare('SELECT * FROM "item" WHERE id = ?');
        $stmt->execute([$this->item->getId()]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function checkCart(): ?array {

        // проверка наличия определенного товара в корзине (получение его цены и количества)

        $stmt = $this->pdo->prepare('SELECT quantity, price FROM "cart" WHERE id = ? AND item_id = ?');
        $stmt->execute([$this->order->getCart()->getId(), $this->item->getId()]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getItemPrice(): float {

        // получение цены товара

        $stmt = $this->pdo->prepare('SELECT price FROM "item" WHERE id = ?');
        $stmt->execute([$this->item->getId()]);

        return $stmt->fetchColumn();
    }

    public function createOrderItem($quantity) {

        try {

            // проверка наличия товара в БД

            if ($this->isExistItem()) {

                $itemPrice = $this->getItemPrice();     // получение цены товара

                // проверка наличия товара в корзине

                if ($this->checkCart()) {

                    throw new ServerException("Ошибка при работе с БД: такой товар уже есть в чеке.");
                }
                else {

                    // в случае отсутствия - добавление новой позиции в чек

                    $stmt = $this->pdo->prepare('INSERT INTO order_item (item_id, order_id, quantity, total_sum)
                                                        VALUES (?, ?, ?, ?) RETURNING id');

                    $stmt->execute([$this->item->getId(), $this->order->getId(), $quantity, $itemPrice]);
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


    public function readOrderItem() {

        try {

            if ($this->id) {

                $stmt = $this->pdo->prepare('SELECT "item".id, "item".name, "item".brand, order_item.price, order_item.quantity FROM order_item
                                                    JOIN "item" ON "item".id = order_item.item_id 
                                                    WHERE order_item.id = ?');

                $stmt->execute([$this->id]);
                $orderItem = $stmt->fetch(PDO::FETCH_ASSOC);

                return $orderItem;
            }
            else {

                throw new ServerException("Ошибка при работе с БД: такого товара нет в чеке.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function updateOrderItem($quantity) {

        try {

            // проверка наличия товара в БД

            if ($this->isExistItem()) {

                $itemPrice = $this->getItemPrice();     // получение цены товара из БД

                // проверка наличия товара в корзине

                if ($itemInCart = $this->checkCart()) {

                    $stmt = $this->pdo->prepare('UPDATE order_item SET quantity = ?, price = ? WHERE id = ?');
                    $stmt->execute([$quantity + $itemInCart['quantity'], $itemPrice * $quantity + $itemInCart['total_sum'], $this->id]);

                    return "Данные о позиции в чеке изменены.";
                }
                else {

                    throw new ServerException("Ошибка при работе с БД: такой товар в корзине не найден.");
                }
            }
            else {

                throw new ServerException("Ошибка при работе с БД: такой товар не найден.");
            }
        } catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function deleteOrderItem() {

        try {

            if ($this->id) {

                $stmt = $this->pdo->prepare('DELETE FROM order_item WHERE id = ?');
                $stmt->execute([$this->id]);

                return "Позиция из чека успешно удалена.";
            }
            else {

                throw new ServerException("Ошибка при работе с БД: такой товар в чеке нет.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
