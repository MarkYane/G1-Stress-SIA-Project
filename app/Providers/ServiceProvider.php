<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OmdbService;
use App\Services\AuthService;
use App\Services\FavoriteService;
use App\Services\SpotifyService;
use App\Services\OpenLibraryService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OmdbService::class, function ($app) {
            return new OmdbService(env('OMDB_API_KEY'));
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });

        $this->app->singleton(FavoriteService::class, function ($app) {
            return new FavoriteService();
        });

        $this->app->singleton(SpotifyService::class, function ($app) {
            return new SpotifyService();
        });

        $this->app->singleton(OpenLibraryService::class, function ($app) {
            return new OpenLibraryService();
        });
    }
}
