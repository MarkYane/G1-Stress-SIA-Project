<?php
namespace App\Http\Controllers;

use App\Services\OpenLibraryService;//
use App\Services\OmdbService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    protected $omdbService;
    protected $openLibraryService; //

    public function __construct(OmdbService $omdbService, OpenLibraryService $openLibraryService, )
    {
        $this->omdbService = $omdbService;
        $this->openLibraryService = $openLibraryService;
    }

    public function searchMovie(Request $request)
    {
        $title = $request->input('t');
        $movie = $this->omdbService->searchMovie($title);
        return response()->json($movie);
    }

    public function searchBooks(Request $request)//
    {
        $params = $request->all();

        try {
            $data = $this->openLibraryService->searchBooks($params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
