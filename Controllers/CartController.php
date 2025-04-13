<?php

class CartController {

    private Cart $cart;

    public function __construct(Cart $cart) {

        $this->cart = $cart;
    }

    public function add($request) {

        $item_id = $request['item_id'] ?? null;
        $quantity = $request['quantity'] ?? null;

        // валидация данных

        if (!is_numeric($item_id) || !is_numeric($quantity) || $quantity <= 0) {

            http_response_code(400);

            return json_encode([
               'status' => 'error',
               'message' => 'Некорректные данные.'
            ]);
        }
        else {

            try {

                // формирование ответа

                $response = $this->cart->addItem($item_id, $quantity);

                if ($response === 'Товар не найден.') {

                    // ошибка в случае отсутствия товара в БД

                    http_response_code(404);

                    return json_encode([
                        'status' => 'error',
                        'message' => $response
                    ]);
                } else {

                    // в случае успешного добавления

                    http_response_code(201);

                    return json_encode([
                        'data' => [
                            'type' => 'cart',
                            'attributes' => $response
                        ]
                    ]);
                }
            }
            catch (Exception $exception) {

                error_log($exception->getMessage());

                http_response_code(500);

                return json_encode([
                    'status' => 'error',
                    'message' => 'Произошла ошибка на сервере.'
                ]);
            }
        }
    }

    public function remove($request) {

        $item_id = $request['item_id'] ?? null;

        // валидация данных

        if (!is_numeric($item_id)) {

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Некорректные данные.'
            ]);
        }
        else {

            try {

                // формирование ответа

                $response = $this->cart->removeItem($item_id);

                if ($response === 'Товар не найден.') {

                    // ошибка в случае отсутствия товара в БД

                    http_response_code(404);

                    return json_encode([
                       'status' => 'error',
                       'message' => $response
                    ]);
                }
                else {

                    // в случае успешного удаления

                    http_response_code(204);

                    return json_encode([
                       'data' => [
                           'type' => 'cart',
                           'attributes' => $response
                       ]
                    ]);
                }
            }
            catch (Exception $exception) {

                error_log($exception->getMessage());

                http_response_code(500);

                return json_encode([
                    'status' => 'error',
                    'message' => 'Ошибка на сервере.'
                ]);
            }
        }
    }

    public function clear() {

        try {

            $response = $this->cart->clearCart();

            if ($response)

        }
        catch (Exception $exception) {

            error_log($exception->getMessage());

            http_response_code(500);

            return json_encode([
                'status' => 'error',
                'message' => 'Ошибка на сервере.'
            ]);
        }


    }
}
