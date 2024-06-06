<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://openlibrary.org/search.json';
    }

    public function searchBooks($params)
    {
        $response = Http::get($this->baseUrl, $params);

        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception('Failed to fetch data from Open Library');
        }
    }
}
