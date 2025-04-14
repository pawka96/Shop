<?php

class ItemController {

    private Item $item;

    public function __construct(Item $item) {

        $this->item = $item;
    }

    public function create($request) {

        $name = $request['name'] ?? null;
        $brand = $request['brand'] ?? null;
        $price = $request['price'] ?? null;
        $category_id = $request['category_id'] ?? null;
        $description = $request['description'] ?? null;

        // валидация данных запроса

        if (is_string($name) && is_string($brand) &&
            is_float($price) && is_numeric($category_id) &&
            is_string($description)) {

            try {

                // формирование ответа

                $response = $this->item->createItem($name, $brand, $price, $category_id, $description);

                if ($response === 'Такой товар уже существует.') {

                    http_response_code(400);

                    return json_encode([
                       'status' => 'error',
                       'message' => $response
                    ]);
                }
                else {

                    http_response_code(201);

                    return json_encode([
                        'data' => [
                            'type' => 'item',
                            'attribute' => [
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
        else {

            http_response_code(400);

            return json_encode([
               'status' => 'error',
               'message' => 'Некорректные данные.'
            ]);
        }
    }

    public function read() {

        try {

            // формирование ответа

            $response = $this->item->readItem();

            if ($response === ServerException::class) {

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
                        'type' => 'item',
                        'attribute' => [
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

    public function update($request) {

        $data = $request['data'] ?? null;

        if (empty($data)) {

            // формирование ошибки в случае отсутствия данных

            http_response_code(400);

            return json_encode([
               'status' => 'error',
               'message' => 'Нет данных для добавления.'
            ]);
        }
        else {

            try {

                // формирование ответа

                $response = $this->item->updateItem($data);

                if ($response === 'Товар не найден.') {

                    // в случае отсутствия товара

                    http_response_code(404);

                    return json_encode([
                        'status' => 'error',
                        'message' => $response
                    ]);
                }
                else {

                    // при успешном обновлении данных

                    http_response_code(202);

                    return json_encode([
                        'data' => [
                            'type' => 'item',
                            'attribute' => [
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

    public function delete() {

        try {

            // формирование ответа

            $response = $this->item->deleteItem();

            if ($response === 'Товар не найден.') {

                // в случае отсутствия товара

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
                        'type' => 'item',
                        'attribute' => [
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
