<?php

namespace App\Services;

use App\Models\Favorite;

class FavoriteService
{
    public function addFavorite($username, $title)
    {
        $favorite = new Favorite();
        $favorite->username = $username;
        $favorite->title = $title;
        $favorite->save();

        return $favorite;
    }

    public function getFavorites($username)
    {
        return Favorite::where('username', $username)->get();
    }
}

