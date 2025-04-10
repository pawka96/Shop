<?php

class UserController {

    private User $user;

    public function __construct() {

    $this->user = new User();
}

    public function register($request) {

        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;
        $name = $request['name'] ?? null;
        $phone_num = $request['phone_num'] ?? null;

        $response = $this->user->registerUser($email, $password, $name, $phone_num);

        // Форматируем ответ в соответствии с JSON API
        return json_encode([
            'data' => [
                'type' => 'users',
                'attributes' => [
                    'message' => $response
                ]
            ]
        ]);
    }


    // Метод для аутентификации пользователя
    public function authenticate($request) {

        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        $response = $this->user->authUser($email, $password);

        // Форматируем ответ в соответствии с JSON API
        return json_encode([
            'data' => [
                'type' => 'users',
                'attributes' => [
                    'message' => $response
                ]
            ]
        ]);
    }
}

