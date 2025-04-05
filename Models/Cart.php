<?php

class Cart
{
    private User $user;

    private int $id, $user_id;

    private PDO $pdo;

    public function __construct($user)
    {

        $this->user = $user;
        $this->user_id = $user->id;
        $this->pdo = $user->pdo;
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
                $stmt->execute([$this->user_id, $item_id]);
                $itemExist = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($itemExist) {

                    // при существовании обновление информации о количестве и полной стоимости

                    $stmt = $this->pdo->prepare('UPDATE cart SET quantity = ?, total_sum = ? WHERE id = ?');
                    $stmt->execute([$quantity + $itemExist['quantity'],
                        $total_sum + $itemExist['total_sum'],
                        $itemExist['id']]);

                    return "Данные в корзине обновлены.";
                } else {

                    // иначе добавление нового товара в корзину

                    $stmt = $this->pdo->prepare('INSERT INTO cart (user_id, item_id, quantity, total_sum) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$this->user_id, $item_id, $quantity, $total_sum]);

                    return "Товар успешно добавлен в корзину.";
                }
            } else {

                return "Товар не найден.";
            }

        } 
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }
}

