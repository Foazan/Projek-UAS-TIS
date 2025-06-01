<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/arts', 'GatewayController@forwardArts');
$router->post('/arts', 'GatewayController@createArt');
$router->delete('/arts/{id}', 'GatewayController@forwardDeleteArt');
$router->get('/arts/user/{user_id}', 'GatewayController@forwardGetArtsByUser');
$router->get('/arts/{id}', 'GatewayController@forwardGetArtById');