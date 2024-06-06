<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SpotifyService;

class SpotifyController extends Controller
{
    protected $spotifyService;
    public function searchTracks(Request $request)
    {
    $query = $request->input('query');
    $tracks = $this->spotifyService->searchTracks($query);
    return response()->json($tracks);
    }

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function redirectToSpotify()
    {
        return $this->spotifyService->redirectToSpotify();
    }

    public function handleSpotifyCallback(Request $request)
    {
        return $this->spotifyService->handleSpotifyCallback($request);
    }

    public function getFavoriteArtist()
    {
        return $this->spotifyService->getFavoriteArtist();
    }

    public function getFavoriteAlbum()
    {
        return $this->spotifyService->getFavoriteAlbum();
    }

    public function controlPlayback($action)
    {
        return $this->spotifyService->controlPlayback($action);
    }

    public function createPlaylist(Request $request)
    {
        return $this->spotifyService->createPlaylist($request);
    }

    public function addToPlaylist(Request $request, $playlistId)
    {
        return $this->spotifyService->addToPlaylist($request, $playlistId);
    }

    public function getRecommendations()
    {
        return $this->spotifyService->getRecommendations();
    }
}
