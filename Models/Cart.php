<?php

class Cart {

    use ItemOperations;

    private int $id;

    private User $user;

    private PDO $pdo;

    public function __construct(User $user)
    {

        $this->user = $user;

        try {
            $this->pdo = new PDO('psql:host=localhost;dbname=shop', 'postgres', 'Hjccbzlkzheccrb[');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при подключении к БД: " . $exception->getMessage());
        }
    }

    public function getUser(): User {

        return $this->user;
    }

    public function getId(): int {

        return $this->id;
    }

    public function createCart($item_id, $quantity) {

        try {

            // проверка наличия записи в БД конкретной корзины

            if ($this->id) {

                throw new ServerException("Ошибка при работе с БД: корзина уже создана.");
            }
            else {

                // иначе создание записи в БД для корзины и добавление первого товара в нее

                // проверка наличия товара в БД

                if ($this->isExist($this->pdo, $item_id)) {

                    $itemPrice = $this->getPrice($this->pdo, $item_id);     // получение цены товара

                    $stmt = $this->pdo->prepare('INSERT INTO cart (user_id, item_id, quantity, total_sum)
                                                        VALUES (?, ?, ?, ?) RETURNING id');
                    $stmt->execute([$this->user->getId(), $item_id, $quantity, $itemPrice * $quantity]);
                    $this->id = $stmt->fetchColumn();

                    return "Добавлен новый товар в корзину.";
                }
                else {

                    throw new ServerException("Ошибка при работе с БД: товар не найден.");
                }
            }
        } catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function readCart() {

        try {

            if ($this->id) {

                $stmt = $this->pdo->prepare('SELECT "item".id, "item".name, "item".brand, "item".price, cart.quantity FROM cart
                                                    JOIN "item" ON "item".id = cart.item_id WHERE cart.id = ?');

                $stmt->execute([$this->id]);

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {

                throw new ServerException("Ошибка при работе с БД: товары в корзине отсутствуют.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function updateCart($item_id, $quantity) {

        try {

            if ($this->id) {

                // проверка наличия товара в БД

                if ($this->isExist($this->pdo, $item_id)) {

                    $itemPrice = $this->getPrice($this->pdo, $item_id);     // получение цены товара

                    // проверка наличия товара в корзине

                    if ($this->checkCart($this->pdo, $this->id, $item_id)) {

                        $stmt = $this->pdo->prepare('UPDATE cart SET quantity = ?, total_sum = ?
                                                            WHERE id = ? AND item_id = ? ');

                        $stmt->execute([$quantity, $itemPrice * $quantity, $this->id, $item_id]);

                        return "Количество товара в корзине изменено.";
                    }
                    else {

                        throw new ServerException("Ошибка при работе с БД: такого товара нет в корзине.");
                    }
                }
                else {

                    throw new ServerException("Ошибка при работе с БД: товар не найден.");
                }
            }
            else {

                throw new ServerException("Ошибка при работе с БД: товары в корзине отсутствуют.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function removeItem($item_id) {

        try {

            if ($this->id) {

                // проверка наличия товара в БД

                if ($this->isExist($this->pdo, $item_id)) {

                    // проверка наличия товара в корзине

                    if ($this->checkCart($this->pdo, $this->id, $item_id)) {

                        $stmt = $this->pdo->prepare('DELETE FROM cart WHERE id = ? AND item_id = ?');
                        $stmt->execute([$this->id, $item_id]);

                        return "Товар из корзингы удален.";
                    }
                    else {

                        throw new ServerException("Ошибка при работе с БД: такого товара нет в корзине.");
                    }
                }
                else {

                    throw new ServerException("Ошибка при работе с БД: товар не найден.");
                }
            }
            else {

                throw new ServerException("Ошибка при работе с БД: товары в корзине отсутствуют.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function deleteCart() {

        try {

            if ($this->id) {

                $stmt = $this->pdo->prepare('DELETE FROM cart WHERE id = ?');
                $stmt->execute([$this->id]);

                return "Корзина полностью очищена.";
            }
            else {

                throw new ServerException("Ошибка при работе с БД: корзина уже пуста.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }

    public function getTotalSum() {

        try {

            if ($this->id) {

            $stmt = $this->pdo->prepare('SELECT SUM(total_sum) as total FROM cart WHERE user_id = ?');
            $stmt->execute([$this->user->getId()]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {

                throw new ServerException("Ошибка при работе с БД: товары в корзине не найдены.");
            }
        }
        catch (PDOException $exception) {

            throw new ServerException("Ошибка при работе с БД: " . $exception->getMessage());
        }
    }
}

