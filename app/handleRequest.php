<?php

require_once 'routes.php';

function handleRequest($method, $uri, $body) {

    global $routes;

    $path = parse_url($uri, PHP_URL_PATH);
    $resources = explode('/', $path);

    $query = parse_url($uri, PHP_URL_QUERY);
    parse_str($query, $params);

    if (!isset($resources[1])) {


    }
    else {
        switch ($resources[1]) {
            case 'about';
            case 'contact';
            case 'user':
                if ($method == 'POST') {

                    $action = $routes['user']['POST'][$path] ?? null;

                    if ($action) {

                        call_user_func_array(new $action[0], $action[1], [$body]);
                    }
                }
                break;

            case 'order':
                switch ($method) {
                    case 'POST' :
                        ;
                    case 'GET':
                        ;
                    case 'PUT' :
                        ;
                    case 'DELETE' :
                        ;
                };
            case 'item':
                switch ($method) {
                    case 'POST' :
                        ;
                    case 'GET':
                        ;
                    case 'PUT' :
                        ;
                    case 'DELETE' :
                        ;
                };
            case 'category':
                switch ($method) {
                    case 'POST' :
                        ;
                    case 'GET':
                        ;
                    case 'PUT' :
                        ;
                    case 'DELETE' :
                        ;
                };
            case 'order_item':
                switch ($method) {
                    case 'POST' :
                        ;
                    case 'GET':
                        ;
                    case 'PUT' :
                        ;
                    case 'DELETE' :
                        ;
                };
            case 'cart':
                switch ($method) {
                    case 'POST' :
                        ;
                    case 'GET':
                        ;
                    case 'PUT' :
                        ;
                    case 'DELETE' :
                        ;
                };
        }
    }
}
}
