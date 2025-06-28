<?php

class User extends BaseModel {

    protected static string $tableName = "\"user\"";

    public static function registerUser($email, $password, $name, $phone_num): string {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            throw new ServerException("Ошибка при регистрации: email не прошел валидацию.");
        }

        self::initPDO();

        try {

            $stmt = self::$pdo->prepare('SELECT * FROM "user" WHERE email = ?');
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
                $stmt = self::$pdo->prepare('INSERT INTO "user" (email, password, name, phone_num) VALUES (?, ?, ?, ?)');
                $stmt->execute([$email, $hashedPassword, $name, $phone_num]);

                return "Регистрация прошла успешно.";
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
    public static function authenticateUser($email, $password): string {

        self::initPDO();

        try {

            $stmt = self::$pdo->prepare('SELECT password FROM "user" WHERE email = ?');
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
