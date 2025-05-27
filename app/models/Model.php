<?php

abstract class Model {

    protected string $table_name;

    protected PDO $pdo;

    public function __construct() {

        try {

            $this->pdo = new PDO('pgsql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function getAll(): ?array {

        try {

            $stmt = $this->pdo->query("SELECT * FROM $this->table_name");

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function showModel(int $id): ?array {

        try {

            $stmt = $this->pdo->prepare("SELECT * FROM $this->table_name WHERE id = ?");
            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function updateModel(int $id, ...$data) {

        try {

            $stmt = $this->pdo->prepare("SELECT FROM $this->table_name WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 1) {

                // формирование частей последующего запроса к БД и соответствующих им значений

                $values = [];
                $keys = [];

                foreach ($data as $key => $value) {

                    $keys[] = "$key = ?";
                    $values[] = $value;
                }

                $sql = "UPDATE $this->table_name SET " . implode(", ", $keys) . " WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([...$values, $id]);

                return "Данные категории успешно обновлены.";
            }
            else {

                throw new ServerException("Ошибка при работе с БД: в таблице $this->table_name не найдено записи с id: $id.");
            }
        }
        catch (PDOException $exception) {

            return "Ошибка при работе с БД: " . $exception->getMessage();
        }
    }

    public function deleteModel(int $id) {

        try {

            $stmt = $this->pdo->prepare("DELETE FROM $this->table_name WHERE id = ?");
            $stmt->execute([$id]);

            return "Запись успешно удалена из таблицы $this->table_name с id: $id.";
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
