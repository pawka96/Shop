<?php

 $routes = [
    'home' => 'HomeController@index',
    'about' => 'AboutController@index',
    'contact' => 'ContactController@index',
    'user' => [
        'POST' => ['/user/register' => 'UserController@register',
                    '/user/auth' => 'UserController@authenticate'
        ],
        'GET' => ['/user' => 'UserController@index',
                    '/user/{id}' => 'UserController@show',
        ],
        'PUT' => ['/user/{id}' => 'UserController@delete'],
        'DELETE' => ['/user/{id}' => 'UserController@delete'],
    ],
    'order' => [
        'POST' => ['/order' => 'OrderController@create'],
        'GET' => ['/order' => 'OrderController@index',
                    '/order/{id}' => 'OrderController@show'
        ],
        'PUT' => ['/order/{id}' => 'OrderController@update'],
        'DELETE' => ['/order/{id}' => 'OrderController@delete'],
    ],
    'item' => [
        'POST' => ['/item' => 'ItemController@create'],
        'GET' => ['/item' => 'ItemController@index',
                    '/item/{id}' => 'ItemController@show'
        ],
        'PUT' => ['/item/{id}' => 'ItemController@update'],
        'DELETE' => ['/item/{id}' => 'ItemController@delete']
    ],
    'category' => [
        'POST' => ['/category' => 'CategoryController@create'],
        'GET' => ['/category' => 'CategoryController@index',
                    '/category/{id}' => 'CategoryController@show'
        ],
        'PUT' => ['/category/{id}' => 'CategoryController@update'],
        'DELETE' => ['/category/{id}' => 'CategoryController@delete']
    ],
    'order_item' => [
        'POST' => ['/order/{id}/item' => 'OrderItemController@create'],
        'GET' => ['/order/{id}/item' => 'OrderItemController@index',
                    '/order/{id}/item/{item_id}' => 'OrderItemController@show'
        ],
        'PUT' => ['/order/{id}/item/{item_id}' => 'OrderItemController@update'],
        'DELETE' => ['/order/{id}/item/{item_id}' => 'OrderItemController@delete']
    ],
    'cart' => [
        'POST' => ['/cart' => 'CartController@create'],
        'GET' => ['/cart/{id}' => 'CartController@show'],
        'PUT' => ['/cart/{id}/item/{item_id}' => 'CartController@update'],
        'DELETE' => ['/cart/{id}' => 'CartController@delete',
                        '/cart/{id}/item/{item_id}' => 'CartController@remove',
        ]
    ]
 ];
