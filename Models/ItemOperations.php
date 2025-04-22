<?php

trait ItemOperations {

    protected function isExist(PDO $pdo, $item_id): bool {

        // проверка наличия товара в БД

        $stmt = $pdo->prepare('SELECT * FROM "item" WHERE id = ?');
        $stmt->execute([$item_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }


    protected function getPrice(PDO $pdo, $item_id): float {

        // получение цены товара

        $stmt = $pdo->prepare('SELECT price FROM "item" WHERE id = ?');
        $stmt->execute([$item_id]);

        return $stmt->fetchColumn();
    }

    protected function checkCart(PDO $pdo, $cart_id, $item_id): ?array {

        // проверка наличия определенного товара в корзине (получение его цены и количества)

        $stmt = $pdo->prepare('SELECT quantity, price FROM cart WHERE id = ? AND item_id = ?');
        $stmt->execute([$cart_id, $item_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
