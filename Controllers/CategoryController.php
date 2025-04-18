<?php

class CategoryController {

    private Category $category;

    public function __construct(Category $category) {

        $this->category = $category;
    }

    public function create($request) {

        $name = $request['name'] ?? null;
        $description = $request['description'] ?? null;

        if (empty($name) || empty($description)) {

            // ошибка в случае отсутствия данных

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Данные для создания отсутствуют.'
            ]);
        }
        else {

            try {

                // формирование ответа при успешном создании

                $response = $this->category->createCategory($name, $description);
                http_response_code(201);

                return json_encode([
                    'data' => [
                        'type' => 'category',
                        'attributes' => [
                            'message' => $response
                        ]
                    ]
                ]);
            }
            catch (ServerException $exception) {

                $exception->handle();
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

    public function read() {

        try {

            // получение ответа при успешном запросе

            $response = $this->category->readCategory();
            http_response_code(200);

            // получение массива товаров

            $items = [];

            foreach ($response as $item) {

                $items[] = [
                    'type' => 'item',
                    'id' => $item['item_id'],
                    'attributes' => [
                        'name' => $item['item_name'],
                    ]
                ];
            }

            return json_encode([
                'data' => [
                    'type' => 'category',
                    'id' => $this->category->getId(),
                    'attributes' => [
                        'name' => $response[0]['name'],
                        'description' => $response[0]['description'],
                        'items' => $items
                    ]
                ]
            ]);
        }
        catch (ServerException $exception) {

            $exception->handle();
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

        $data = $request['data'] ?? null;

        if (empty($data)) {

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Нет данных для добавления.'
            ]);
        }
        else {

            try {

                // формирование ответа при удачном обновлении

                $response = $this->category->updateCategory($data);

                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'category',
                        'id' => $this->category->getId(),
                        'attributes' => [
                            'message' => $response
                        ]
                    ]
                ]);
            }
            catch (ServerException $exception) {

                $exception->handle();
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

    public function delete() {

        try {

            // формирование ответа при успешном удалении

            $response = $this->category->deleteCategory();

            http_response_code(204);

            return json_encode([
                'data' => [
                    'type' => 'item',
                    'id' => $this->category->getId(),
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
