<?php

class Category {

    private int $id;

    private PDO $pdo;

    public function __construct() {

        try {
            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            error_log($exception->getMessage());
            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function createCategory($name, $description) {

        // проверка на дубли в БД

        try {

            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM category WHERE name = ?');
            $stmt->execute([$name]);

            if ($stmt->fetchColumn() > 0) {

                return "Такая категория уже существует.";
            }
            else {

                $stmt = $this->pdo->prepare('INSERT INTO category (name, description) VALUES (?, ?) RETURNING id');
                $stmt->execute([$name, $description]);
                $this->id = $stmt->fetchColumn();

                return "Новая категория добавлена успешно в БД.";
            }
        }
        catch (PDOException $exception) {

            error_log($exception->getMessage());
            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function readCategory() {

        // проверка на наличие категории в БД

        try {

            if ($this->id) {

                $stmt = $this->pdo->prepare('SELECT category.name, category.description, "item".name FROM category
                                                    JOIN "item" ON "item".category_id = category.id
                                                    WHERE category.id = ?');
                $stmt->execute([$this->id]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);

                return $category;
            }
            else {

                return "Такой категории нет в БД.";
            }
        }
        catch (PDOException $exception) {

            error_log($exception->getMessage());
            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function updateCategory(...$data) {

        // проверка на наличие категории в БД

        try {

            if ($this->id) {

                if (empty($data)) {

                    return "Нет данных для добавления.";
                } else {

                    // формирование частей последующего запроса к БД и соответствующих им значений

                    $values = [];
                    $keys = [];

                    foreach ($data as $key => $value) {

                        $keys[] = "$key = ?";
                        $values[] = $value;
                    }

                    $sql = 'UPDATE category SET ' . implode(", ", $keys) . ' WHERE id = ?';
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([...$values, $this->id]);

                    return "Данные категории успешно обновлены.";
                }
            }
            else {

                return "Такой категории нет в БД.";
            }
        }
        catch (PDOException $exception) {

            error_log($exception->getMessage());
            return  "Ошибка при работе с БД: " . $exception->getMessage();
        }
    }

    public function deleteCategory() {

        // проверка на наличие категории в БД

        try {

            if ($this->id) {

               $stmt = $this->pdo->prepare('DELETE FROM category WHERE id = ?');
               $stmt->execute([$this->id]);

               return "Категория успешно удалена.";
            }
            else {

                return "Такой категории нет в БД.";
            }
        }
        catch (PDOException $exception) {

            error_log($exception->getMessage());
            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
