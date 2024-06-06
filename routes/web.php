<?php
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\ApiGatewayController;
use Illuminate\Support\Facades\Route;

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
$router->get('/search/tracks', 'SpotifyController@searchTracks');
$router->get('spotify/redirect', 'SpotifyController@redirectToSpotify');
$router->get('spotify/callback', 'SpotifyController@handleSpotifyCallback');
$router->get('spotify/favorite-artist', 'SpotifyController@getFavoriteArtist');
$router->get('spotify/favorite-album', 'SpotifyController@getFavoriteAlbum');
$router->post('spotify/control-playback/{action}', 'SpotifyController@controlPlayback');
$router->post('spotify/create-playlist', 'SpotifyController@createPlaylist');
$router->post('spotify/add-to-playlist/{playlistId}', 'SpotifyController@addToPlaylist');
$router->get('spotify/recommendations', 'SpotifyController@getRecommendations');




