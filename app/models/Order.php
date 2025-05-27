<?php

class Order extends Model {

    protected string $table_name = "\"order\"";

    public function createModel($user_id, $date, $total_sum, $status) {

        try {

            // создание нового заказа

            $stmt = $this->pdo->prepare('INSERT INTO "order" (user_id, date, total_sum, status) VALUES(?, ?, ?, ?)');
            $stmt->execute([$user_id, $date, $total_sum, $status]);

            return "Заказ успешно создан";
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
