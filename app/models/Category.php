<?php

class Category extends Model {

    protected string $table_name = "category";

    public function createModel($name, $description) {

        // проверка на дубли в БД

        try {

            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM category WHERE name = ?');
            $stmt->execute([$name]);

            if ($stmt->fetchColumn() > 0) {

                throw new ServerException("Ошибка при подключении к БД: такая категория уже существует.");
            }
            else {

                $stmt = $this->pdo->prepare('INSERT INTO category (name, description) VALUES (?, ?)');
                $stmt->execute([$name, $description]);

                return "Новая категория успешно создана.";
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
