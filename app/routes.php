<?php

$routes = [
    '/' => 'HomeController@index',
    'about' => 'AboutController@index',
    'contact' => 'ContactController@index',
    'user' => [
        'POST' => ['/register' => 'UserController@register',
                    '/auth' => 'UserController@authenticate'
        ],
        'GET' => ['/user' => 'UserController@index',
                    '/user/{id}' => 'UserController@show',
        ],
        'PUT /user/{id}' => 'UserController@delete',
        'DELETE /user/{id}' => 'UserController@delete',
    ],
    'order' => [
        'POST' => 'OrderController@create',
        'GET' => 'OrderController@read',
        'PUT' => 'OrderController@update',
        'DELETE' => 'OrderController@delete'
    ],
    'item' => [
        'POST' => 'ItemController@create',
        'GET' => 'ItemController@read',
        'PUT' => 'ItemController@update',
        'DELETE' => 'ItemController@delete'
    ],
    'category' => [
        'POST' => 'CategoryController@create',
        'GET' => 'CategoryController@read',
        'PUT' => 'CategoryController@update',
        'DELETE' => 'CategoryController@delete'
    ],
    'order_item' => [
        'POST' => 'OrderItemController@create',
        'GET' => 'OrderItemController@read',
        'PUT' => 'OrderItemController@update',
        'DELETE' => 'OrderItemController@delete'
    ],
    'cart' => [
        'POST /cart' => 'CartController@create',
        'GET /cart/{id}' => 'CartController@show',
        'PUT /cart/{id}/item/{item_id}' => 'CartController@update',
        'DELETE' => ['/cart/{id}' => 'CartController@delete',
                        '/cart/{id}/item/{item_id}' => 'CartController@remove',
        ]
    ]
];


function handleRequest($uri) {
    switch ($uri) {
        case '/products':
            require_once 'controllers/ProductController.php';
            $controller = new ProductController();
            $controller->index();
            break;
        case '/products/create':
            // Код для обработки создания продукта
            break;
        // Другие маршруты...
        default:
            // Обработка 404
            break;
    }
}