<?php

require_once 'C:\PHP\projects\Shop\app\handleRequest.php';
require_once 'C:\PHP\projects\Shop\app\routes.php';

// Инициализация приложения

$requestMethod = "GET"; //$_SERVER['REQUEST_METHOD'];
$requestUri = "/user"; //$_SERVER['REQUEST_URI'];
$requestBody = ""; //json_decode(file_get_contents('php://input'), true);
error_log("Request: $requestMethod $requestUri");

try {

    handleRequest($requestMethod, $requestUri, $requestBody, $routes);  // Функция для обработки запроса
}
catch (ServerException $exception) {

    return $exception->handle();
}
catch (Exception $exception) {

    error_log($exception->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'error',
        'message' => $exception->getMessage()
    ]);

    exit;
}