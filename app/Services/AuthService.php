<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function signup($username, $password)
    {
        if (User::find($username)) {
            return response()->json(['error' => 'Username already exists'], 400);
        }

        $user = new User();
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->save();

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function validateUser($username, $password)
    {
        $user = User::find($username);

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        return true;
    }
}
