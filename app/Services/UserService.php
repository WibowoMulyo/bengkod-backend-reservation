<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUserData($userId)
    {
        return User::findOrFail($userId);
    }

    public function getUserProfile($userId)
    {
        $user  = User::select('name', 'email_mhs', 'photo')->where('id', $userId)->first();
        
        if ($user && $user->photo) {
            $user->photo = url("storage/photos/{$user->photo}");
        }
        return $user;
    }

    public function updateUserData($userId, array $data)
    {
        $user = User::findOrFail($userId);
        $user->update($data);
        return $user;
    }
}
