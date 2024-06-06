<?php
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\ApiGatewayController;
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

// routes/web.php



$router->get('/omdb/search', 'GatewayController@searchMovie');
$router->get('/openlibrary/search', 'GatewayController@searchBooks');//



