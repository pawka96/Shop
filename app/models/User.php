<?php

class User {

    private int $id;

    private PDO $pdo;

    public function __construct() {

        try {

            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function getId(): int {

        return $this->id;
    }

    public function getAllUsers(): ?array {

        try {

            $stmt = $this->pdo->query('SELECT id, name, email, password, phone_num, status FROM "user"');

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

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
                $stmt = $this->pdo->prepare('INSERT INTO "user" (email, password, name, phone_num)
                                                    VALUES (?, ?, ?, ?) RETURNING id');

                $stmt->execute([$email, $hashedPassword, $name, $phone_num]);
                $this->id = $stmt->fetchColumn();

                return "Вы успешно зарегистрированы.";
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

    public function showUser(int $id): ?array {

        try {

            $stmt = $this->pdo->query('SELECT id, name, email, password, phone_num, status
                                                FROM "user" WHERE id = ?');

            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function updateUser(...$data) {

        try {

            if ($this->id) {

                // формирование частей последующего запроса к БД и соответствующих им значений

                $keys = [];
                $values = [];

                foreach ($data as $key => $value) {

                    $keys[] = "$key = ?";
                    $values[] = $value;
                }

                $stmt = $this->pdo->prepare('UPDATE "user" SET ' . implode(", ", $keys) . ' WHERE id = ?');
                $stmt->execute([...$values, $this->id]);

                return "Данные пользователя успешно обновлены.";
            }
            else {

                throw new ServerException("Ошибка при работе с БД: пользователь не найден.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function deleteUser(int $id) {

        try {

            $stmt = $this->pdo->prepare('DELETE FROM "user" WHERE id = ?');
            $stmt->execute([$this->id]);

            return "Пользователь успешно удален.";
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}
