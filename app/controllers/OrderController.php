<?php

class OrderController {

    private Order $order;

    public function __construct(Order $order) {

        $this->order = $order;
    }

    public function index() {

        try {

            // формирование успешного ответа

            $response = $this->order->getAllOrders();
            http_response_code(200);

            // получение массива заказов

            $orders = [];

            foreach ($response as $order) {

                $orders[] = [
                    'type' => 'order',
                    'id' => $order['id'],
                    'attributes' => [
                        'user_id' => $order['user_id'],
                        'date' => $order['date'],
                        'total_sum' => $order['total_sum'],
                        'status' => $order['status']
                    ]
                ];
            }

            return json_encode(['data' => $orders]);
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

    public function create($request) {

        $user_id = $request['name'] ?? null;
        $date = $request['brand'] ?? null;
        $total_sum = $request['price'] ?? null;
        $status = $request['category_id'] ?? null;

        // валидация данных запроса

        if (is_numeric($user_id) && strtotime($date) &&
            is_float($total_sum) && is_string($status)) {

            try {

                // формирование ответа при успешном создании

                $response = $this->order->createOrder($user_id, $date, $total_sum, $status);
                http_response_code(201);

                return json_encode([
                    'data' => [
                        'type' => 'order',
                        'id' => $this->order->getId(),
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

            // формирование ошибки в случае неверных данных

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Неверные данные.'
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

                $response = $this->order->showOrder($id);
                http_response_code(200);

                return json_encode([
                    'data' => [
                        'type' => 'order',
                        'id' => $response['id'],
                        'attributes' => [
                            'user_id' => $response['user_id'],
                            'date' => $response['date'],
                            'total_sum' => $response['total_sum'],
                            'status' => $response['status']
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

        $status = $request['status'] ?? null;

        // валидация статуса

        if ($status == 'оплачен' || $status === "выполнен") {

            try {

                // формирование ответа при успешном обновлении

                $response = $this->order->updateOrder($status);
                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'order',
                        'id' => $this->order->getId(),
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

            // формирование ошибки при некорректном статусе

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Некорректный статус.'
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

                $response = $this->order->deleteOrder($id);
                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'order',
                        'id' => $this->order->getId(),
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
