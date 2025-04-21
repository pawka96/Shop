<?php

function isExist(PDO $pdo, $item_id): bool {

    // проверка наличия товара, и получение его стоимости в случае успешного нахождения

    $stmt = $pdo->prepare('SELECT price FROM "item" WHERE id = ?');
    $stmt->execute([$item_id]);

    return $stmt->fetchColumn(PDO::FETCH_ASSOC) !== false;
}
