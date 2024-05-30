<?php

namespace App\Http\Controllers;

use App\Services\OmdbService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    protected $omdbService;

    public function __construct(OmdbService $omdbService)
    {
        $this->omdbService = $omdbService;
    }

    public function searchMovie(Request $request)
    {
        $title = $request->input('t');
        $movie = $this->omdbService->searchMovie($title);
        return response()->json($movie);
    }
}