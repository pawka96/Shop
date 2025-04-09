<?php

class Cart {

    private int $id;

    private User $user;

    private PDO $pdo;

    public function __construct(User $user)
    {

        $this->user = $user;

        try {
            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function getUser() {

        return $this->user;
    }

    public function getId() {

        return $this->id;
    }

    public function addItem($item_id, $quantity) {

        try {

            // проверка наличия товара в БД

            $stmt = $this->pdo->prepare('SELECT price FROM item WHERE id = ?');
            $stmt->execute([$item_id]);
            $itemPrice = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($itemPrice) {

                $total_sum = $quantity * $itemPrice['price'];

                // проверка на то, есть ли этот товар уже в корзине

                $stmt = $this->pdo->prepare('SELECT id, quantity, total_sum FROM cart WHERE user_id = ? AND item_id = ?');
                $stmt->execute([$this->user->getId(), $item_id]);
                $itemExist = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($itemExist) {

                    // при существовании обновление информации о количестве и полной стоимости

                    $stmt = $this->pdo->prepare('UPDATE cart SET quantity = ?, total_sum = ? WHERE id = ?');
                    $stmt->execute([$quantity + $itemExist['quantity'],
                        $total_sum + $itemExist['total_sum'],
                        $itemExist['id']]);

                    return "Данные в корзине обновлены.";
                }
                else {

                    // иначе добавление нового товара в корзину

                    $stmt = $this->pdo->prepare('INSERT INTO cart (user_id, item_id, quantity, total_sum)
                                                        VALUES (?, ?, ?, ?) RETURNING id');
                    $stmt->execute([$this->user->getId(), $item_id, $quantity, $total_sum]);
                    $this->id = $stmt->fetchColumn();

                    return "Товар успешно добавлен в корзину.";
                }
            }
            else {

                return "Товар не найден.";
            }
        } 
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function removeItem($item_id) {

        try {

            $stmt = $this->pdo->prepare('DELETE FROM cart WHERE item_id = ? AND user_id = ?');
            $stmt->execute([$item_id, $this->user->getId()]);

            return "Товар удален из корзины.";
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function cleanCart() {

        try {

            $stmt = $this->pdo->prepare('DELETE FROM cart WHERE user_id = ?');
            $stmt->execute([$this->user->getId()]);

            return "Корзина полностью очищена.";
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function showItems() {

        try {

            $stmt = $this->pdo->prepare('SELECT "item".name, cart.quantity, cart.total_sum FROM cart
                                                JOIN "item" ON "item".id = cart.item_id WHERE cart.user_id = ?');
            $stmt->execute([$this->user->getId()]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($items) {

                return $items;
            }
            else {

                return "Ваша корзина пуста.";
            }
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function getTotalSum() {

        try {

            $stmt = $this->pdo->prepare('SELECT SUM(total_sum) as total FROM cart WHERE user_id = ?');
            $stmt->execute([$this->user->getId()]);
            $total_sum = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($total_sum) {

                return "Полная сумма покупки: " . $total_sum['total'];
            }
            else {

                return "Ваша корзина пуста.";
            }
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }
}

