<?php

class ItemController {

    private Item $item;

    public function __construct(Item $item) {

        $this->item = $item;
    }

    public function index() {

        try {

            // формирование успешного ответа

            $response = $this->item->getAllItems();
            http_response_code(200);

            // получение массива товаров

            $items = [];

            foreach ($response as $item) {

                $items[] = [
                    'type' => 'item',
                    'id' => $item['id'],
                    'attributes' => [
                        'name' => $item['name'],
                        'brand' => $item['brand'],
                        'price' => $item['price'],
                        'description' => $item['description'],
                        'category' => [
                            'id' => $item['category_id'],
                            'attributes' => [
                                'name' => $item['category_name']
                            ]
                        ]
                    ]
                ];
            }

            return json_encode(['data' => $items]);
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

                // формирование ответа при успешном создании

                $response = $this->item->createItem($name, $brand, $price, $category_id, $description);
                http_response_code(201);

                return json_encode([
                    'data' => [
                        'type' => 'item',
                        'id' => $this->item->getId(),
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

                $response = $this->item->showItem($id);
                http_response_code(200);

                return json_encode([
                    'data' => [
                        'type' => 'item',
                        'id' => $id,
                        'attributes' => [
                            'name' => $response['name'],
                            'brand' => $response['brand'],
                            'price' => $response['price'],
                            'description' => $response['description'],
                            'category' => [
                                'id' => $response['cat_id'],
                                'attributes' => [
                                    'name' => $response['cat_name']
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

                // формирование ответа при успешном обновлении данных

                $response = $this->item->updateItem($data);
                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'item',
                        'id' => $this->item->getId(),
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

                $response = $this->item->deleteItem($id);
                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'item',
                        'id' => $this->item->getId(),
                        'attribute' => [
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
