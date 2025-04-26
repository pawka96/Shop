<?php

$routes = [
    '/' => 'HomeController@index',
    'about' => 'AboutController@index',
    'contact' => 'ContactController@index',
    'users' => [
        'POST' => 'UserController@register',
        'GET' => 'UserController@authenticate'
    ],
    'orders' => [
        'POST' => 'OrderController@create',
        'GET' => 'OrderController@read',
        'PUT' => 'OrderController@update',
        'DELETE' => 'OrderController@delete'
    ],
    'items' => [
        'POST' => 'ItemController@create',
        'GET' => 'ItemController@read',
        'PUT' => 'ItemController@update',
        'DELETE' => 'ItemController@delete'
    ],
    'categories' => [
        'POST' => 'CategoryController@create',
        'GET' => 'CategoryController@read',
        'PUT' => 'CategoryController@update',
        'DELETE' => 'CategoryController@delete'
    ],
    'order_items' => [
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

/*$routes = [
    'cart' => [
        'POST' => 'CartController@add', // Добавить товар в корзину
        'GET' => [
            'show' => 'CartController@show', // Показать содержимое корзины
            'sum' => 'CartController@getSum', // Получить сумму товаров
        ],
        'DELETE' => [
            '{item_id}' => 'CartController@remove', // Удалить конкретный товар из корзины
            'clear' => 'CartController@clear' // Очистить всю корзину
        ]
    ],
];*/

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