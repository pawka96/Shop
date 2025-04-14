<?php

class CartController {

    private Cart $cart;

    public function __construct(Cart $cart) {

        $this->cart = $cart;
    }

    public function add($request) {

        $item_id = $request['item_id'] ?? null;
        $quantity = $request['quantity'] ?? null;

        // валидация данных запроса

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
                }
                else {

                    // в случае успешного добавления

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
            }
            catch (ServerException $exception) {

                return $exception->handle();
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
                           'attributes' => [
                               'message' => $response
                           ]
                       ]
                    ]);
                }
            }
            catch (ServerException $exception) {

                return $exception->handle();
            }
        }
    }

    public function clear() {

        try {

            $response = $this->cart->clearCart();

            if ($response === 'Ваша корзины пуста.') {

                // в случае пустой корзины

                http_response_code(404);

                return json_encode([
                   'status' => 'error',
                   'message' => $response
                ]);
            }
            else {

                // в случае успешной очистки корзины

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
        }
        catch (ServerException $exception) {

            return $exception->handle();
        }
    }

    public function show() {

        try {

            // формирование ответа

            $response = $this->cart->showItems();

            if ($response === 'Ваша корзина пуста.') {

                http_response_code(404);

                return json_encode([
                   'status' => 'error',
                   'message' => $response
                ]);
            }
            else {

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
        }
        catch (ServerException $exception) {

            return $exception->handle();
        }
    }

    public function getSum() {

        try {

            // формирование ответа

            $response = $this->cart->getTotalSum();

            if ($response === 'Ваша корзина пуста.') {

                http_response_code(404);

                return json_encode([
                    'status' => 'error',
                    'message' => $response
                ]);
            }
            else {

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
        }
        catch (ServerException $exception) {

            return $exception->handle();
        }
    }
}
