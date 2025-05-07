<?php

class OrderItemController {

    private OrderItem $orderItem;

    public function __construct(OrderItem $orderItem) {

        $this->orderItem = $orderItem;
    }

    public function index($request) {

        $order_id = $request['name'] ?? null;

        // валидация данных запроса

        if (!is_numeric($order_id)) {

            // формирование ошибки в случае неверных данных

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Неверные данные.'
            ]);
        }
        else {

            try {

                // формирование успешного ответа

                $response = $this->orderItem->getAllOrderItems($order_id);
                http_response_code(200);

                // получение массива позиций чека

                $orderItems = [];

                foreach ($response as $orderItem) {

                    $orderItems[] = [
                        'type' => 'order_item',
                        'id' => $orderItem['id'],
                        'attributes' => [
                            'quantity' => $orderItem['quantity'],
                            'price' => $orderItem['price'],
                            'item' => [
                                'id' => $orderItem['item_id'],
                                'name' => $orderItem['name'],
                            ],
                            'order' => [
                                'id' => $order_id,
                                'date' => $orderItem['date'],
                            ]
                        ]
                    ];
                }

                return json_encode(['data' => $orderItems]);
            }
            catch (Exception $exception) {

                error_log($exception->getMessage());
                http_response_code(500);

                return json_encode([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ]);
            }
        }
    }

    public function create($request) {

        $order_id = $request['order_id'] ?? null;
        $item_id = $request['item_id'] ?? null;
        $quantity = $request['quantity'] ?? null;

        // валидация данных

        if (is_int($quantity) && $quantity > 0 && is_numeric($order_id) && is_numeric($item_id)) {

            try {

                // формирование ответа при успешном добавлении

                $response = $this->orderItem->createOrderItem($order_id, $item_id, $quantity);
                http_response_code(201);

                return json_encode([
                    'data' => [
                        'type' => 'order_item',
                        'id' => $this->orderItem->getId(),
                        'attributes' => [
                            'message' => $response
                        ]
                    ]
                ]);
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
        }
        else {

            // формирование ошибки в случае некорректного запроса

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Некорректные данные.'
            ]);
        }
    }

    public function show($request) {

        $id = $request['id'] ?? null;

        if (!is_numeric($id)) {

            // формирование ошибки в случае неверных данных

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Неверный формат данных.'
            ]);
        }
        else {

            try {

                // формирование успешного ответа

                $response = $this->orderItem->showOrderItem($id);
                http_response_code(200);

                return json_encode([
                    'data' => [
                        'type' => 'order_item',
                        'id' => $id,
                        'attributes' => [
                            'quantity' => $response['quantity'],
                            'price' => $response['price'],
                            'item' => [
                                'id' => $response['item_id'],
                                'name' => $response['name'],
                            ],
                            'order' => [
                                'id' => $response['order_id'],
                                'date' => $response['date'],
                            ]
                        ]
                    ]
                ]);
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
        }
    }

    public function update($request) {

        $order_id = $request['order_id'] ?? null;
        $item_id = $request['item_id'] ?? null;
        $quantity = $request['quantity'] ?? null;

        // валидация данных

        if (is_int($quantity) && $quantity > 0 && is_numeric($order_id) && is_numeric($item_id)) {

            try {

                // формирование ответа при успешном обновлении

                $response = $this->orderItem->updateOrderItem($order_id, $item_id, $quantity);
                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'order_item',
                        'id' => $this->orderItem->getId(),
                        'attributes' => [
                            'message' => $response
                        ]
                    ]
                ]);
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
        }
        else {

            // формирование ошибки в случае некорректного запроса

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Некорректные данные.'
            ]);
        }
    }

    public function delete($request) {

        $id = $request['id'] ?? null;

        if (!is_numeric($id)) {

            // формирование ошибки в случае неверных данных

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Неверный формат данных.'
            ]);
        }
        else {

            try {

                // формирование ответа при успешном удалении

                $response = $this->orderItem->deleteOrderItem($id);
                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'order_item',
                        'id' => $id,
                        'attributes' => [
                            'message' => $response
                        ]
                    ]
                ]);
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
        }
    }
}
