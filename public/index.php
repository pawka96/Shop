<?php

require_once '../app/handleRequest.php';

// Инициализация приложения

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$requestBody = json_decode(file_get_contents('php://input'), true);
error_log("Request: $requestMethod $requestUri");

try {

    handleRequest($requestMethod, $requestUri, $requestBody);  // Функция для обработки запроса
}
catch (ServerException $exception) {

    return $exception->handle();
}
catch (Exception $exception) {

    error_log($exception->getMessage());
    http_response_code(500);

    return json_encode([
        'status' => 'error',
        'message' => $exception->getMessage()
    ]);
}