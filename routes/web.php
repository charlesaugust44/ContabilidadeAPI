<?php

use Laravel\Lumen\Routing\Router;

$prefix = 'api/v1';

$router->group(['prefix' => "$prefix/user"], function (Router $router) {
    $router->post('', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@create'
    ]);

    $router->get('/token', 'UsersController@token');

    $router->get('/{id}', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@read'
    ]);

    $router->get('', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@readAll'
    ]);

    $router->put('/{id}', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@update'
    ]);

    $router->delete('/{id}', [
        'middleware' => 'authAdmin',
        'uses' => 'UsersController@delete'
    ]);
});

$router->group(['prefix' => "$prefix/client"], function (Router $router) {
    $router->post('', [
        'middleware' => 'authOther',
        'uses' => 'ClientsController@create'
    ]);

    $router->get('', [
        'middleware' => 'authOther',
        'uses' => 'ClientsController@readAll'
    ]);

    $router->get('/deleted', [
        'middleware' => 'authOther',
        'uses' => 'ClientsController@readAllDeleted'
    ]);

    $router->get('/nondeleted', [
        'middleware' => 'authOther',
        'uses' => 'ClientsController@readAllNonDeleted'
    ]);

    $router->get('/{id}', [
        'middleware' => 'authOther',
        'uses' => 'ClientsController@read'
    ]);

    $router->get('/{id}/modifications', [
        'middleware' => 'authAdmin',
        'uses' => 'ClientModificationsController@readAllByClient'
    ]);

    $router->get('/{id}/debts', [
        'middleware' => 'authOther',
        'uses' => 'DebtsController@readAllByClient'
    ]);

    $router->put('/{id}', [
        'middleware' => 'authOther',
        'uses' => 'ClientsController@update'
    ]);

    $router->delete('/{id}', [
        'middleware' => 'authOther',
        'uses' => 'ClientsController@delete'
    ]);
});

$router->group(['prefix' => "$prefix/debt"], function (Router $router) {
    $router->post('', [
        'middleware' => 'authOther',
        'uses' => 'DebtsController@create'
    ]);

    $router->delete('/{id}', [
        'middleware' => 'authAdmin',
        'uses' => 'DebtsController@delete'
    ]);
});