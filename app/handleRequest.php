<?php

require_once 'routes.php';

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

        $action = explode("@", $routes[$resources[0]]);

        if (is_array($action) && count($action) === 2) {

            call_user_func(new $action[0], $action[1]);
        }
        else {

            throw new ServerException("Ошибка при получении маршрута ресурса: $resources[0].");
        }
    }
    else {

        if (!array_key_exists($method, $routes[$resources[0]])) {

            throw new ServerException("Ошибка при получении маршрута ресурса: $resources[0] с методом $method.");
        }
        else {

            $action = explode("@", $routes[$resources[0]][$method][$path]);

            if (is_array($action) && count($action) === 2) {

                if (!empty($body)) {

                    call_user_func_array([new $action[0], $action[1]], $body);
                }
                else {

                    call_user_func(new $action[0], $action[1]);
                }
            }
            else {

                throw new ServerException("Ошибка при получении маршрута ресурса: $resources[0].");
            }
        }
    }
}
