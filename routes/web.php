<?php

use Laravel\Lumen\Routing\Router;

$router->group(['prefix' => 'contabilidade/api/'], function (Router $router) {
    $router->post('user', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@create'
    ]);

    $router->get('user/{id}', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@read'
    ]);

    $router->get('user', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@readAll'
    ]);

    $router->put('user/{id}', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@update'
    ]);

    $router->delete('user/{id}', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@delete'
    ]);

    $router->get('user/token', 'UsersController@token');
});

