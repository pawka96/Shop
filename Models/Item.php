<?php

class Item {

    private int $id;
    private PDO $pdo;

    public function __construct() {

        try {
            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            throw new Exception("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function getId(): int {

        return $this->id;
    }

    public function createItem($name, $brand, $price, $category_id, $description) {

        try {

            // проверка на дубли в БД

            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM "item" WHERE name = ? AND brand = ?');
            $stmt->execute([$name, $brand]);

            if ($stmt->fetchColumn() > 0) {

                return "Такой товар уже существует.";
            }
            else {

                $stmt = $this->pdo->prepare('INSERT INTO "item" (category_id, name, brand, price, description)
                                                    VALUES (?, ?, ?, ?, ?) RETURNING id;');
                $stmt->execute([$category_id, $name, $brand, $price, $description]);
                $this->id = $stmt->fetchColumn();

                return "Новый товар успешно добавлен в БД.";
            }
        }
        catch (PDOException $exception) {

            throw new Exception("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function readItem() {

        try {

            if ($this->id) {

                $stmt = $this->pdo->prepare('SELECT "item".name, "item".brand, "item".price,
                                                    "item".description, category.name FROM "item"
                                                    JOIN category ON category.id = "item".category_id
                                                    WHERE item.id = ?');
                $stmt->execute([$this->id]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);

                return $item;
            }
            else {

                return "Такого товара нет в БД.";
            }
        }
        catch (PDOException $exception) {

            throw new Exception("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function updateItem(...$data) {

        try {

            if ($this->id) {

                if (empty($data)) {

                    return "Нет данных для добавления.";
                }
                else {

                    // формирование частей последующего запроса к БД и соответствующих им значений

                    $keys = [];
                    $values = [];

                    foreach ($data as $key => $value) {

                        $keys[] = "$key = ?";
                        $values[] = $value;
                    }

                    $sql = 'UPDATE "item" SET ' . implode(", ", $keys) . ' WHERE id = ?';
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([...$values, $this->id]);

                    return "Данные товара успешно обновлены.";
                }
            }
            else {

                return "Товар не найден.";
            }
        }
        catch (PDOException $exception) {

            throw new Exception("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function deleteItem() {

        try {

            if ($this->id) {

                $stmt = $this->pdo->prepare('DELETE FROM "item" WHERE id = ?');
                $stmt->execute([$this->id]);

                return "Товар успешно удален.";
            }
            else {

                return "Товар не найден.";
            }
        }
        catch (PDOException $exception) {

            throw new Exception("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}