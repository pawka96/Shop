<?php

class CartController {

    private Cart $cart;

    public function __construct(Cart $cart) {

        $this->cart = $cart;
    }

    public function add($request) {

        $item_id = $request['item_id'] ?? null;
        $quantity = $request['quantity'] ?? null;

        if (!is_numeric($item_id) || !is_numeric($quantity) || $quantity <= 0) {

            // формирование ошибки в случае некорректных данных

            http_response_code(400);

            return json_encode([
               'status' => 'error',
               'message' => 'Некорректные данные.'
            ]);
        }
        else {

            try {

                // формирование ответа при успешном добавлении

                $response = $this->cart->addItem($item_id, $quantity);
                http_response_code(201);

                return json_encode([
                    'data' => [
                        'type' => 'cart',
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

    public function remove($request) {

        $item_id = $request['item_id'] ?? null;

        if (!is_numeric($item_id)) {

            // формирование ошибки в случае некорректных данных

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Некорректные данные.'
            ]);
        }
        else {

            try {

                // формирование ответа при успешном удалении

                $response = $this->cart->removeItem($item_id);

                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'cart',
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

    public function clear()
    {

        try {

            $response = $this->cart->clearCart();

            // в случае успешной очистки корзины

            http_response_code(200);

            return json_encode([
                'data' => [
                    'type' => 'cart',
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

    public function show() {

        try {

            // формирование успешного ответа

            $response = $this->cart->showItems();

            http_response_code(200);

            return json_encode([
                'data' => [
                    'type' => 'cart',
                    'id' => $this->cart->getId(),
                    'attributes' => [
                        'item_name' => $response['name'],
                        'item_brand' => $response['brand'],
                        'item_quantity' => $response['quantity'],
                        'item_price' => $response['price'],
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

    public function getSum() {

        try {

            // формирование успешного ответа

            $response = $this->cart->getTotalSum();

            http_response_code(200);

            return json_encode([
                'data' => [
                    'type' => 'cart',
                    'id' => $this->cart->getId(),
                    'attributes' => [
                        'total_sum' => $response
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
