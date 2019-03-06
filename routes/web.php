<?php

use Laravel\Lumen\Routing\Router;

$router->group(['prefix' => 'contabilidade/api/'], function (Router $router) {
    $router->post('user', 'UsersController@create');
    $router->get('user/{id}', 'UsersController@read');
    $router->get('user', 'UsersController@readAll');
    $router->put('user/{id}','UsersController@update');
    $router->delete('user/{id}','UsersController@delete');
});