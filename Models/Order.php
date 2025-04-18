<?php

class Order {

    private int $id;

    private PDO $pdo;

    private Cart $cart;

    public function __construct(Cart $cart) {

        $this->cart = $cart;

        try {
            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());;
        }
    }

    public function getId(): int {

        return $this->id;
    }

    public function getCart(): Cart {

        return $this->cart;
    }

    public function createOrder() {

        try {

            // проверка наличия в БД записей о пользователе и корзине

            $stmt = $this->pdo->prepare('SELECT cart.total_sum FROM cart
                                                JOIN "user" ON "user".id = cart.user_id
                                                WHERE cart.id = ?');

            $stmt->execute([$this->cart->getId()]);

            if ($total_sum = $stmt->fetchColumn()) {

                $stmt = $this->pdo->prepare('INSERT INTO "order" (user_id, date, total_sum, status)
                                                    VALUES (?, NOW(), ?, создан) RETURNING id');

                $stmt->execute([$this->cart->getUser()->getId(),$total_sum]);
                $this->id = $stmt->fetchColumn();

                return "Заказ успешно создан";
            }
            else {

                throw new ServerException("Ошибка при работе с БД: товары в корзину не добавлены.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function readOrder()
    {

        try {

            // проверка наличия заказа в БД

            if ($this->id) {

                $stmt = $this->pdo->prepare('SELECT "user".id, "user".name, "user".email, "user".phone_num,
                                                    "order".date, "order".total_sum, "order".status FROM "order"
                                                    JOIN "user" ON "user".id = "order".user_id
                                                    WHERE "order".id = ?');

                $stmt->execute([$this->id]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                return $order;
            }
            else {

                throw new ServerException("Ошибка при работе с БД: такой заказ не найден.");
            }
        }
        catch (PDOException $exception) {

            error_log($exception->getMessage());
            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function updateOrder($status) {

        if ($this->id) {

            try {

                $stmt = $this->pdo->prepare('UPDATE "order" SET "status" = ? WHERE id = ?');
                $stmt->execute([$status, $this->id]);

                return "Новый статус заказа: " . $status;
            }
            catch (PDOException $exception) {

                throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
            }
        }
        else {

            throw new ServerException("Ошибка при работе с БД: такой заказ не найден.");
        }
    }

    public function deleteOrder() {

        try {

            if ($this->id) {

                $stmt = $this->pdo->prepare('DELETE FROM "order" WHERE id = ?');
                $stmt->execute([$this->id]);

                return "Заказ успешно удален.";
            }
            else {

                throw new ServerException("Ошибка при работе с БД: такой заказ не найден.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
