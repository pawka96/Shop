<?php

class Category extends BaseModel {

    protected static string $tablName = "category";

    public static function createModel($name, $description) {

        // проверка на дубли в БД

        try {

            $stmt = self::$pdo->prepare('SELECT COUNT(*) FROM category WHERE name = ?');
            $stmt->execute([$name]);

            if ($stmt->fetchColumn() > 0) {

                throw new ServerException("Ошибка при подключении к БД: такая категория уже существует.");
            }
            else {

                $stmt = self::$pdo->prepare('INSERT INTO category (name, description) VALUES (?, ?)');
                $stmt->execute([$name, $description]);

                return "Новая категория успешно создана.";
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
