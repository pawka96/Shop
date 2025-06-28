<?php

require_once 'C:\PHP\projects\Shop\app\ServerException.php';

abstract class BaseModel {

    protected static string $tableName;

    protected static ?PDO $pdo = null;

    public static function initPDO(): void {

        if (self::$pdo === null) {

            try {

                self::$pdo = new PDO('pgsql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $exception) {

                throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
            }
        }
    }

    public static function getAll(): ?array {

        self::initPDO();

        try {

            $stmt = self::$pdo->query("SELECT * FROM " . static::$tableName);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function showModel(int $id): ?array {

        self::initPDO();

        try {

            $stmt = self::$pdo->prepare("SELECT * FROM " .  static::$tableName . " WHERE id = ?");
            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public static function updateModel(int $id, ...$data): string {

        self::initPDO();

        try {

            $stmt = self::$pdo->prepare("SELECT FROM " . static::$tableName . " WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 1) {

                // формирование частей последующего запроса к БД и соответствующих им значений

                $values = [];
                $keys = [];

                foreach ($data as $key => $value) {

                    $keys[] = "$key = ?";
                    $values[] = $value;
                }

                $sql = "UPDATE " . static::$tableName . " SET " . implode(", ", $keys) . " WHERE id = ?";
                $stmt = self::$pdo->prepare($sql);
                $stmt->execute([...$values, $id]);

                return "Данные категории успешно обновлены.";
            }
            else {

                throw new ServerException("Ошибка при работе с БД: в таблице "
                                                    . static::$tableName . " не найдено записи с id: $id.");
            }
        }
        catch (PDOException $exception) {

            return "Ошибка при работе с БД: " . $exception->getMessage();
        }
    }

    public static function deleteModel(int $id): string {

        self::initPDO();

        try {

            $stmt = self::$pdo->prepare("DELETE FROM " . static::$tableName . " WHERE id = ?");
            $stmt->execute([$id]);

            return "Запись успешно удалена из таблицы " . static::$tableName . " с id: $id.";
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
