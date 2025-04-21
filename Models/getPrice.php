<?php

function getPrice(PDO $pdo, $item_id): float {

    // получение цены товара

    $stmt = $pdo->prepare('SELECT price FROM "item" WHERE id = ?');
    $stmt->execute([$item_id]);

    return $stmt->fetchColumn();
}