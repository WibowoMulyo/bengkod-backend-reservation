<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUserData($userId)
    {
        return User::findOrFail($userId);
    }

    public function updateUserData($userId, array $data)
    {
        $user = User::findOrFail($userId);
        $user->update($data);
        return $user;
    }
}