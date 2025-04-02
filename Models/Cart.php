<?php

class Cart {

    private int $id, $user_id, $item_id;

    private float $total_sum;

    public function __construct()
    {

        session_start();

        if (!isset($_SESSION['cart'])) {

            $_SESSION['cart'] = [];
        }
    }

    ///////////// Посмотреть насчет этого метода
        public function add($itemId, $quantity) {
            if (isset($_SESSION['cart'][$itemId])) {
                $_SESSION['cart'][$itemId] += $quantity; // Увеличиваем количество
            } else {
                $_SESSION['cart'][$itemId] = $quantity; // Добавляем новый товар
            }

    }

  /*  public function addItem($id, $count) {

        try {

            $stmt = $this->pdo->prepare('SELECT price FROM item WHERE id = ?');
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item) {

                $stmt = $this->pdo->prepare('INSERT INTO cart (user_id, item_id, total_sum) VALUES (?, ?, ?)');
                $stmt->execute([$this->id, $item['id'], $count * $item['price']]);

                return "Товар успешно добавлен в корзину.";
            } else {

                return "Товар не найден.";
            }
        } catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    } */

    public function removeItem() {}
}
