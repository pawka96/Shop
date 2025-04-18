<?php

class OrderItemController {

    private OrderItem $orderItem;

    public function __construct(OrderItem $orderItem) {

        $this->orderItem = $orderItem;
    }

    public function create($request) {

        $quantity = $request['quantity'] ?? null;

        // валидация данных

        if (is_int($quantity) && $quantity > 0) {

            try {

                // формирование ответа при успешном добавлении

                $response = $this->orderItem->createOrderItem($quantity);
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
               'message' => 'Некорректное количество товаров.'
            ]);
        }
    }

    public function read() {

        try {

            // формирование успешного ответа

            $response = $this->orderItem->readOrderItem();
            http_response_code(200);

            return json_encode([
                'data' => [
                    'type' => 'order_item',
                    'id' => $this->orderItem->getId(),
                    'attributes' => [
                        'price' => $response['price'],
                        'quantity' => $response['quantity'],
                        'item' => [
                            'type' => 'item',
                            'id' => $response['id'],
                            'attributes' => [
                                'name' => $response['name'],
                                'brand' => $response['brand']
                            ]
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

    public function update($request) {

        $quantity = $request['quantity'] ?? null;

        if (is_int($quantity) && $quantity > 0) {

            try {

                // формирование ответа при успешном обновлении

                $response = $this->orderItem->updateOrderItem($quantity);
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
                'message' => 'Некорректное количество товаров.'
            ]);
        }
    }

    public function delete() {

        try {

            // формирование ответа при успешном удалении

            $response = $this->orderItem->deleteOrderItem();
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
}
