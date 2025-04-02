<?php

namespace Project\Models;
use PDO;

class User {

    public int $id;
    public string $name, $email, $password, $phone_num;
    public bool $isConfirmed;

    public function registerUser($email, $password) {

        $pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
        $stmt = $pdo -> prepare('SELECT * FROM "user" WHERE email = ?');
        $stmt -> execute([$email]);

        if ($stmt -> rowCount() > 0) {

            return "Такой Email уже существует.";
        }
        else {

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt -> prepare('INSERT INTO "user" ("email", "password") VALUES (?, ?)');
            $stmt -> execute([$email, $hashedPassword]);
        }
    }
}
