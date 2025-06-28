<?php

class Order extends BaseModel {

    protected static string $tableName = "\"order\"";

    public function createModel($user_id, $date, $total_sum, $status) {

        try {

            // создание нового заказа

            $stmt = self::$pdo->prepare('INSERT INTO "order" (user_id, date, total_sum, status) VALUES(?, ?, ?, ?)');
            $stmt->execute([$user_id, $date, $total_sum, $status]);

            return "Заказ успешно создан";
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
