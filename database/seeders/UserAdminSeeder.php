<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email_mhs' => 'admin@admin.com',
            'password' => bcrypt('rahasia12'),
            'photo' => 'default.jpg',
            'penalty_count' => 0,
            'is_admin' => true,
        ]);
    }
}
