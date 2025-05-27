<?php

require_once 'routes.php';
require_once '../app/models';
require_once '../app/controllers';

function handleRequest($method, $uri, $body) {

    global $routes;

    // получение пути запроса и ресурсов

    $path = parse_url($uri, PHP_URL_PATH);
    $resources = explode('/', trim($path, "/"));

    $query = parse_url($uri, PHP_URL_QUERY);
    parse_str($query, $params);

    if (empty($resources[0])) {

        throw new ServerException("Ошибка в пути запроса: путь не указан.");
    }
    elseif (!array_key_exists($resources[0], $routes)) {

        throw new ServerException("Ресурс не найден: $resources[0].");
    }
    elseif (in_array($resources[0], ['home', 'about', 'contact'])) {

        // обработка ресурсов 'home', 'about', 'contact'

        $action = explode("@", $routes[$resources[0]]);

        if (is_array($action) && count($action) === 2) {

            // в случае успешной проверки, вызов функции контроллера

            call_user_func(new $action[0], $action[1]);
        }
        else {

            throw new ServerException("Ошибка при получении маршрута ресурса: $resources[0].");
        }
    }
    else {

        // обработка остальных ресурсов

        if (!array_key_exists($method, $routes[$resources[0]])) {

            // ошибка, если не найден метод с маршрутом в массиве

            throw new ServerException("Ошибка при получении маршрута ресурса: $resources[0] с методом $method.");
        }
        else {

            // в случае успешного нахождения в массиве маршрутов

            $action = explode("@", $routes[$resources[0]][$method][$path]);

            if (is_array($action) && count($action) === 2) {

                $model = new (ucfirst($resources[0]))();       // создание экземпляра модели с получением его имени с заглавной буквы
                $controller = new $action[0]($model);       // создание экземпляра контроллера

                if (!empty($body)) {

                    // вызов функции контроллера, если имеется тело запроса

                    call_user_func_array([$controller, $action[1]], $body);
                }
                else {

                    // вызов функции контроллера без тела запроса

                    call_user_func($controller, $action[1]);
                }
            }
            else {

                throw new ServerException("Ошибка при получении маршрута ресурса: $resources[0].");
            }
        }
    }
}
