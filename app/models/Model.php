<?php

abstract class Model {

    private int $id;

    private PDO $pdo;

    public function __construct() {

        try {

            $this->pdo = new PDO('pgsql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function getTableName(): string {

        $class = new ReflectionClass($this);
        $table_name = strtolower($class->getShortName());

        if (in_array($table_name,["user", "item", "order"])) {

            $table_name = "\"$table_name\"";
        }

        return $table_name;
    }

    public function getAll(): ?array {

        $table_name = $this->getTableName();

        try {
            $stmt = $this->pdo->query("SELECT * FROM $table_name");

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function getId(): int {

        return $this->id;
    }

    public function show(int $id): ?array {

        $table_name = $this->getTableName();

        try {

            $stmt = $this->pdo->prepare('SELECT * FROM $table_name WHERE id = ?');
            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }
}
