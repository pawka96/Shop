<?php

class UserController {

    private User $user;

    public function __construct(User $user) {

        $this->user = $user;
    }

    public function index() {

        try {

            // формирование успешного ответа

            $response = $this->user->getAllUsers();
            http_response_code(200);

            // получение массива пользователей

            $users = [];

            foreach ($response as $user) {

                $users[] = [
                    'type' => 'user',
                    'id' => $user['id'],
                    'attributes' => [
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'password' => $user['password'],
                        'phone_num' => $user['phone_num'],
                        'status' => $user['status']
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

    public function register($request) {

        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;
        $name = $request['name'] ?? null;
        $phone_num = $request['phone_num'] ?? null;

        if (empty($email) || empty($password) || empty($name) || empty($phone_num)) {

            // формирование ошибки в случае отсутствия данных

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Все поля должны быть заполнены.'
            ]);
        }
        else {

            try {

                // формирование ответа при успешной регистрации

                $response = $this->user->registerUser($email, $password, $name, $phone_num);
                http_response_code(201);

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

    public function authenticate($request)
    {

        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        if (empty($email) || empty($password)) {

            // формирование ошибки в случае отсутствия данных

            http_response_code(400);

            return json_encode([
                'status' => 'error',
                'message' => 'Все поля должны быть заполнены.'
            ]);
        } else {

            try {

                // формирование ответа при успешной аутентификации

                $response = $this->user->authenticateUser($email, $password);
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

    public function index()
    {

        try {

            // формирование ответа для получения всех пользователей

            $response = $this->user->getAllUsers();
            http_response_code(200);

            $users = [];

            // получение массива пользователей

            foreach ($response as $user) {

                $users[] = [
                    'type' => 'user',
                    'id' => $response['id'],
                    'attributes' => [
                        'name' => $response['name'],
                        'email' => $response['email'],
                        'phone_num' => $response['phone_num'],
                        'password' => $response['password'],
                    ]
                ];
            }

            return json_encode(['data' => [$users]]);
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
