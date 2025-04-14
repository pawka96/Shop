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

                // формирование ответа

                $response = $this->user->registerUser($email, $password, $name, $phone_num);

                // в случае неудачной регистрации

                if ($response === 'Некорректный email'
                    || $response === 'Такой Email уже зарегистрирован.'
                    || $response === 'Пароль должен содержать 8 и более символов.') {

                    http_response_code(400);

                    return json_encode([
                        'status' => 'error',
                        'message' => $response

                    ]);
                } else {

                    // в случае успешной регистрации

                    http_response_code(200);

                    return json_encode([
                        'data' => [
                            'type' => 'user',
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

                // формирование ответа

                $response = $this->user->authUser($email, $password);

                // в случае неудачной аутентификации

                if ($response === 'Введены неверные данные.') {

                    http_response_code(401);

                    return json_encode([
                        'status' => 'error',
                        'message' => $response
                    ]);
                }
                else {

                    // в случае успешной аутентификации

                    http_response_code(200);

                    return json_encode([
                        'data' => [
                            'type' => 'user',
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
}
