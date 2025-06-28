<?php

class Item extends BaseModel {

    protected static string $tableName = "\"item\"";

    public static function createModel($name, $brand, $price, $category_id, $description): string {

        try {

            // проверка на дубли в БД

            $stmt = self::$pdo->prepare('SELECT COUNT(*) FROM "item" WHERE name = ? AND brand = ?');
            $stmt->execute([$name, $brand]);

            if ($stmt->fetchColumn() > 0) {

                throw new ServerException("Ошибка при работе с БД:
                                                    товар с таким названием ($name) и такого бренда ($brand) уже существует.");
            }
            else {

                $stmt = static::$pdo->prepare('INSERT INTO "item" (category_id, name, brand, price, description)
                                                    VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$category_id, $name, $brand, $price, $description]);

                return "Новый товар успешно добавлен в БД.";
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public static function showFull(int $id): ?array {

        try {

            $stmt = self::$pdo->prepare('SELECT "item".id, "item".name, "item".brand, category.id as cat_id,
                                                category.name as cat_name, "item".price, "item".description 
                                                FROM "item" JOIN category ON category.id = "item".category_id
                                                WHERE item.id = ?');

            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}