<?php

class Category {

    private PDO $pdo;

    public function __construct() {

        try {
            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function createCategory($name, $brand, $price, $category_id, $description) {


    }
}
