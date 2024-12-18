<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function getUserData($userId)
    {
        $user =  User::findOrFail($userId);

        if ($user->photo) {
            $user->photo = url("storage/photos/{$user->photo}");
        }

        return $user;
    }
    public function getUserProfile($userId)
    {
        $user  = User::select('name', 'email_mhs', 'photo')->where('id', $userId)->first();

        if ($user && $user->photo) {
            $user->photo = url("storage/photos/{$user->photo}");
        }
        return $user;
    }

    public function updateUserData(array $data)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        if(isset($data['photo'])){
            if ($user->photo) {
                Storage::delete('public/photos/' . $user->photo);
            }

            $photoPath = $data['photo']->store('public/photos');
            $data['photo'] = basename($photoPath);
        }

        $user->update($data);
        return (object)[];
    }
    public function destroy(int $userIdFromUrl)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        if ($user->photo) {
            $photoPath = 'public/photos/' . $user->photo;

            if (Storage::exists($photoPath)) {
                Storage::delete($photoPath);
            }

            $user->update(['photo' => 'default.jpg']);
        }
        return (object)[];
    }
}
