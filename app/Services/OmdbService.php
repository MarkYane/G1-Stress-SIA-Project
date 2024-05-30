<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OmdbService
{
    protected $apiKey;
    protected $baseUrl = 'http://www.omdbapi.com/';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function searchMovie($t)
    {
        $response = Http::get($this->baseUrl, [
            'apikey' => $this->apiKey,
            't' => $t,
        ]);

        return $response->json();
    }
}