<?php


// Пример использования контроллера
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$requestBody = json_decode(file_get_contents('php://input'), true);

$controller = new UserController();

if ($requestMethod === 'POST' && $requestUri === '/register') {
    header('Content-Type: application/json');
    echo $controller->register($requestBody);
} elseif ($requestMethod === 'POST' && $requestUri === '/authenticate') {
    header('Content-Type: application/json');
    echo $controller->authenticate($requestBody);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
