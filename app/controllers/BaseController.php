<?php

abstract class BaseController {

    protected static string $modelClass;

    public static function index() {

        try {

            // формирование успешного ответа

            $response = static::$modelClass::getAll();
            http_response_code(200);

            // получение массива данных

            $datas = [];
            $attrs = [];

            foreach ($response as $row => $column) {

                $datas[] = [
                    'type' => strtolower(static::$modelClass),
                    'id' => $row['id'],
                    'attributes' => [
                        foreach ($column as $name => $value) {

                            $name = $value;
                            $attrs = '$name => $value,' . PHP_EOL; 
                        }
                        $attrs;
                    ]
                ];
            }

            return json_encode(['data' => $users]);
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

                $response = $this->user->showUser($id);
                http_response_code(200);

                return json_encode([
                    'data' => [
                        'type' => 'user',
                        'id' => $id,
                        'attributes' => [
                            'name' => $response['name'],
                            'email' => $response['email'],
                            'password' => $response['password'],
                            'phone_num' => $response['phone_num'],
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

                // формирование успешного ответа

                $response = $this->user->updateUser($data);
                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'user',
                        'id' => $this->user->getId(),
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

                // формирование успешного ответа

                $response = $this->user->deleteUser($id);
                http_response_code(204);

                return json_encode([
                    'data' => [
                        'type' => 'user',
                        'id' => $this->user->getId(),
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
