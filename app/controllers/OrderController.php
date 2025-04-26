<?php

class OrderController {

    private Order $order;

    public function __construct(Order $order)
    {

        $this->order = $order;
    }

    public function create()
    {

        try {

            // формирование ответа при успешном создании

            $response = $this->order->createOrder();
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
        } catch (ServerException $exception) {

            return $exception->handle();
        } catch (Exception $exception) {

            error_log($exception->getMessage());
            http_response_code(500);

            return json_encode([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function read()
    {

        try {

            // формирование успешного ответа

            $response = $this->order->readOrder();
            http_response_code(200);

            return json_encode([
                'data' => [
                    'type' => 'order',
                    'id' => $this->order->getId(),
                    'attributes' => [
                        'date' => $response['date'],
                        'total_sum' => $response['total_sum'],
                        'status' => $response['status'],
                        'user' => [
                            'type' => 'user',
                            'id' => $response["id"],
                            'attributes' => [
                                'name' => $response["name"],
                                'email' => $response["email"],
                                'phone_num' => $response["phone_num"]
                            ]
                        ]
                    ]
                ]
            ]);
        } catch (ServerException $exception) {

            return $exception->handle();
        } catch (Exception $exception) {

            error_log($exception->getMessage());
            http_response_code(500);

            return json_encode([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function update($request)
    {

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
            } catch (ServerException $exception) {

                return $exception->handle();
            } catch (Exception $exception) {

                error_log($exception->getMessage());
                http_response_code(500);

                return json_encode([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ]);
            }
        } else {

            // формирование ошибки при некорректном статусе

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Некорректный статус.'
            ]);
        }
    }

    public function delete()
    {

        try {

            // формирование ответа при успешном удалении

            $response = $this->order->deleteOrder();
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
        } catch (ServerException $exception) {

            return $exception->handle();
        } catch (Exception $exception) {

            error_log($exception->getMessage());
            http_response_code(500);

            return json_encode([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }
}
