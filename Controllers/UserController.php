<?php

class UserController {

    private User $user;

    public function __construct (User $user) {

        $this->user = $user;
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

    public function authenticate($request) {

        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        if (empty($email) || empty($password)) {

            // формирование ошибки в случае отсутствия данных

            http_response_code(400);

            return json_encode([
               'status' => 'error',
               'message' => 'Все поля должны быть заполнены.'
            ]);
        }
        else {

            try {

                // формирование ответа при успешной аутентификации

                $response = $this->user->authUser($email, $password);
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
