<?php

class User extends Model {

    protected string $table_name = "\"user\"";

    public function registerUser($email, $password, $name, $phone_num) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            throw new ServerException("Ошибка при регистрации: email не прошел валидацию.");
        }

        try {

            $stmt = $this->pdo->prepare('SELECT * FROM "user" WHERE email = ?');
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {

                throw new ServerException("Ошибка при регистрации: такой email уже зарегистрирован.");
            }
            elseif (strlen($password) < 8) {

                throw new ServerException("Ошибка при регистрации: пароль должен содержать 8 и более символов.");
            }
            else {

                // при успешной регистрации

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $this->pdo->prepare('INSERT INTO "user" (email, password, name, phone_num) VALUES (?, ?, ?, ?)');
                $stmt->execute([$email, $hashedPassword, $name, $phone_num]);

                return "Регистрация прошла успешно.";
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
    public function authenticateUser($email, $password) {

        try {

            $stmt = $this->pdo->prepare('SELECT password FROM "user" WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                return "Вход выполнен успешно.";
            }
            else {

                throw new ServerException("Ошибка при аутентификации: введены неверные данные.", 401);
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
