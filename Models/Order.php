<?php

class Order {

    private int $id;

    private PDO $pdo;

    private Cart $cart;

    private User $user;

    public function __construct(Cart $cart) {

        $this->cart = $cart;
        $this->user = $cart->getUser();

        try {
            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function createOrder() {

        try {

            // проверка наличия в БД записей о пользователе и корзине

            $stmt = $this->pdo->prepare('SELECT cart.total_sum FROM cart
                                                JOIN "user" ON "user".id = cart.user_id
                                                WHERE "user".id = ? AND cart.id = ?');
            $stmt->execute([$this->user->getId(), $this->cart->getId()]);

            if ($total_sum = $stmt->fetchColumn()) {

                $stmt = $this->pdo->prepare('INSERT INTO "order" (user_id, date, total_sum, status)
                                                    VALUES (?, NOW(), ?, создан) RETURNING id');

                $stmt->execute([$this->user->getId(),$total_sum]);
                $this->id = $stmt->fetchColumn();

                return "Заказ успешно создан";
            }
            else {

                return "Товары в корзину не добавлены.";
            }
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function readOrder()
    {

        try {

            // проверка наличия заказа в БД

            if ($this->id) {

                $stmt = $this->pdo->prepare('SELECT "user".name, "user".email, "user".phone_num,
                                                    "order".date, "order".total_sum, "order".status FROM "order"
                                                    JOIN "user" ON "user".id = "order".user_id
                                                    WHERE "order".id = ? AND "user".id = ?');
                $stmt->execute([$this->id, $this->user->getId()]);

                if ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    return $order;
                } else {

                    return "С таким заказом пользователь не найден.";
                }
            } else {

                return "Такого заказа нет.";
            }
        } catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function updateOrder($status) {

        if ($status == 'оплачен' || $status === "выполнен") {

            if ($this->id) {

                try {

                    $stmt = $this->pdo->prepare('UPDATE "order" SET "status" = ? WHERE id = ?');
                    $stmt->execute([$status, $this->id]);
                }
                catch (PDOException $exception) {

                    return "Ошибка: " . $exception->getMessage();
                }
            } else {

                return "Такого заказа нет.";
            }
        }
        else {

            return "Неверный формат статуса.";
        }
    }
    
}
