<?php

require_once '../app/handleRequest.php';

// Инициализация приложения

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$requestBody = json_decode(file_get_contents('php://input'), true);

handleRequest($requestMethod, $requestUri,$requestBody);  // Функция для обработки запроса