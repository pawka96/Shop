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

    public function getAllOrders(): ?array {

        try {

            $stmt = $this->pdo->query('SELECT id, user_id, date, total_sum, status FROM "order"');

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function createOrder($user_id, $date, $total_sum, $status)
    {

        try {

            // проверка на существование заказа

            if ($this->id) {

                throw new ServerException("Ошибка при работе с БД: такой заказ уже создан.");
            }
            else {

                // создание нового заказа

                $stmt = $this->pdo->prepare('INSERT INTO "order" (user_id, date, total_sum, status)
                                                    VALUES(?, ?, ?, ?) RETURNING id');

                $stmt->execute([$user_id, $date, $total_sum, $status]);
                $this->id = $stmt->fetchColumn();

                return "Заказ успешно создан";
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function showOrder(int $id): ?array {

        try {

            $stmt = $this->pdo->prepare('SELECT id, user_id, date, total_sum, status
                                                FROM "order" WHERE "order".id = ?');

            $stmt->execute([$id]);

            return $$stmt->fetch(PDO::FETCH_ASSOC) ?: null;
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
