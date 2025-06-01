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
$router->get('/arts', function () {
    return response()->json([
        ['id' => 1, 'title' => 'Karya A', 'artist' => 'Jundi'],
        ['id' => 2, 'title' => 'Karya B', 'artist' => 'Fauzan'],
    ]);
});
$router->get('/arts', 'ArtController@index');
$router->post('/arts', 'ArtController@store');
$router->delete('/arts/{id}', 'ArtController@destroy');
$router->get('/arts/user/{user_id}', 'ArtController@getByUser');
$router->get('/arts/{id}', 'ArtController@getById');