<?php
namespace App\Http\Controllers;

use App\Services\OpenLibraryService;
use App\Services\AuthService;
use App\Services\OmdbService;
use App\Services\FavoriteService;
use App\Services\SpotifyService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    protected $authService;
    protected $omdbService;
    protected $favoriteService;
    protected $spotifyService;
    protected $openLibraryService;

    public function __construct(AuthService $authService, OmdbService $omdbService, FavoriteService $favoriteService, SpotifyService $spotifyService, OpenLibraryService $openLibraryService)
    {
        $this->authService = $authService;
        $this->omdbService = $omdbService;
        $this->favoriteService = $favoriteService;
        $this->spotifyService = $spotifyService;
        $this->openLibraryService = $openLibraryService;
    }

    public function handleRequest(Request $request)
    {
        $request->validate([
            'action' => 'required|in:signup,search_movie,get_favorites,spotify_favorite_artist,spotify_favorite_album,spotify_control_playback,spotify_create_playlist,spotify_add_to_playlist,spotify_recommendations,search_books',
            'username' => 'required',
            'password' => 'required',
            'title' => 'required_if:action,search_movie',
            'add_to_favorite' => 'required_if:action,search_movie|in:yes,no',
            'get_all_favorites' => 'required_if:action,get_favorites|in:yes',
            'playlist_name' => 'required_if:action,spotify_create_playlist',
            'playlist_description' => 'required_if:action,spotify_create_playlist',
            'public' => 'required_if:action,spotify_create_playlist|boolean',
            'track_uris' => 'required_if:action,spotify_add_to_playlist',
            'playlist_id' => 'required_if:action,spotify_add_to_playlist',
            'spotify_action' => 'required_if:action,spotify_control_playback|in:play,pause,next,previous',
        ]);

        $action = $request->input('action');
        $username = $request->input('username');
        $password = $request->input('password');

        switch ($action) {
            case 'signup':
                return $this->authService->signup($username, $password);

            case 'search_movie':
                $title = $request->input('title');
                $addToFavorite = $request->input('add_to_favorite', 'no');
                return $this->searchMovie($username, $password, $title, $addToFavorite);

            case 'get_favorites':
                $getAllFavorites = $request->input('get_all_favorites');
                return $this->getFavorites($username, $password, $getAllFavorites);

            case 'spotify_favorite_artist':
                return $this->spotifyService->getFavoriteArtist();

            case 'spotify_favorite_album':
                return $this->spotifyService->getFavoriteAlbum();

            case 'spotify_control_playback':
                $spotifyAction = $request->input('spotify_action');
                return $this->spotifyService->controlPlayback($spotifyAction);

            case 'spotify_create_playlist':
                return $this->spotifyService->createPlaylist($request);

            case 'spotify_add_to_playlist':
                $playlistId = $request->input('playlist_id');
                return $this->spotifyService->addToPlaylist($request, $playlistId);

            case 'spotify_recommendations':
                return $this->spotifyService->getRecommendations();

            case 'search_books':
                return $this->searchBooks($request);

            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }

    private function searchMovie($username, $password, $title, $addToFavorite)
    {
        if (!$this->authService->validateUser($username, $password)) {
            return response()->json(['error' => 'Username or password is incorrect'], 401);
        }

        $movie = $this->omdbService->searchMovie($title);

        if ($addToFavorite === 'yes') {
            $this->favoriteService->addFavorite($username, $title);
        }

        return response()->json($movie);
    }

    public function searchBooks(Request $request)
    {
        $params = $request->all();

        try {
            $data = $this->openLibraryService->searchBooks($params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getFavorites($username, $password, $getAllFavorites)
    {
        if ($getAllFavorites !== 'yes') {
            return response()->json(['error' => 'Invalid parameter value'], 400);
        }

        if (!$this->authService->validateUser($username, $password)) {
            return response()->json(['error' => 'Username or password is incorrect'], 401);
        }

        $favorites = $this->favoriteService->getFavorites($username);
        return response()->json($favorites);
    }
}
