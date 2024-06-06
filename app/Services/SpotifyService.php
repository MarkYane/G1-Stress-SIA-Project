<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SpotifyService
{
    public function redirectToSpotify()
    {
        $query = http_build_query([
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'response_type' => 'code',
            'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
            'scope' => 'user-read-private user-read-email user-library-read user-library-modify user-follow-read user-follow-modify user-read-playback-state user-modify-playback-state user-read-currently-playing user-read-recently-played playlist-modify-public playlist-modify-private',
        ]);

        return redirect('https://accounts.spotify.com/authorize?' . $query);
    }

    public function handleSpotifyCallback(Request $request)
    {
        $code = $request->get('code');

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        ]);

        $accessToken = $response->json()['access_token'];

        Session::put('spotify_access_token', $accessToken);
        Log::info('Access token set in session', ['token' => $accessToken]);

        return redirect('/spotify');
    }

    private function getAccessToken()
    {
        $accessToken = Session::get('spotify_access_token');
        Log::info('Access token retrieved from session', ['token' => $accessToken]);
        return $accessToken;
    }

    private function makeSpotifyRequest($method, $url, $options = [])
    {
        $accessToken = $this->getAccessToken();

        $client = new Client([
            'base_uri' => 'https://api.spotify.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        $response = $client->request($method, $url, $options);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getFavoriteArtist()
    {
        return $this->makeSpotifyRequest('GET', 'me/top/artists');
    }

    public function getFavoriteAlbum()
    {
        return $this->makeSpotifyRequest('GET', 'me/albums');
    }

    public function controlPlayback($action)
    {
        switch ($action) {
            case 'play':
                return $this->makeSpotifyRequest('PUT', 'me/player/play');
            case 'pause':
                return $this->makeSpotifyRequest('PUT', 'me/player/pause');
            case 'next':
                return $this->makeSpotifyRequest('POST', 'me/player/next');
            case 'previous':
                return $this->makeSpotifyRequest('POST', 'me/player/previous');
            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }

    public function createPlaylist(Request $request)
    {
        $userId = $this->makeSpotifyRequest('GET', 'me')['id'];
        $playlistData = [
            'name' => $request->input('playlist_name'),
            'description' => $request->input('playlist_description', ''),
            'public' => $request->input('public', false),
        ];

        return $this->makeSpotifyRequest('POST', "users/$userId/playlists", [
            'json' => $playlistData
        ]);
    }

    public function addToPlaylist(Request $request, $playlistId)
    {
        $trackUris = $request->input('track_uris');
        return $this->makeSpotifyRequest('POST', "playlists/$playlistId/tracks", [
            'json' => ['uris' => $trackUris]
        ]);
    }

    public function getRecommendations()
    {
        return $this->makeSpotifyRequest('GET', 'recommendations', [
            'query' => [
                'seed_artists' => 'artist_id',
                'seed_tracks' => 'track_id',
                'seed_genres' => 'genre',
            ]
        ]);
    }
}
