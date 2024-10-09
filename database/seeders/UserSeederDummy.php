<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeederDummy extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'), // Pastikan password di-hash
            'photo' => 'default_photo.png',
            'is_admin' => true,
        ]);
    }
}

