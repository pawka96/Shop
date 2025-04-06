<?php

class User {

    private PDO $pdo;

    public function __construct() {

        try {
            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception->getMessage();
        }
    }

    public function registerUser($email, $password) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            return "Некорректный email.";
        }

        try {

            $stmt = $this->pdo->prepare('SELECT * FROM "user" WHERE email = ?');
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {

                return "Такой Email уже существует.";
            }
            else {

                if (strlen($password) < 8) {

                    return "Пароль должен содержать 8 и более символов.";
                }

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $this->pdo->prepare('INSERT INTO "user" ("email", "password") VALUES (?, ?)');
                $stmt->execute([$email, $hashedPassword]);

                return "Вы успешно зарегистрированы.";
            }
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception -> getMessage();
        }
    }

    public function authUser($email, $password) {

        try {

            $stmt = $this->pdo->prepare('SELECT password FROM "user" WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                return "Вход выполнен успешно.";
            }
            else {

                return "Введены неверные данные.";
            }
        }
        catch (PDOException $exception) {

            return "Ошибка: " . $exception -> getMessage();
        }
    }
}
