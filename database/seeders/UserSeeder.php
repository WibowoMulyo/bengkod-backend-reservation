<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Farhan',
                'email_mhs' => '123456789016@mhs.dinus.ac.id',
                'password' => bcrypt('password'),
                'photo' => 'default.jpg',
                'ban_until' => null,
                'is_admin' => false,
            ],
            [
                'name' => 'Dina',
                'email_mhs' => '123456789017@mhs.dinus.ac.id',
                'password' => bcrypt('password'),
                'photo' => 'default.jpg',
                'ban_until' => null,
                'is_admin' => false,
            ],
            [
                'name' => 'Ahmad',
                'email_mhs' => '123456789018@mhs.dinus.ac.id',
                'password' => bcrypt('password'),
                'photo' => 'default.jpg',
                'ban_until' => null,
                'is_admin' => false,
            ],
            [
                'name' => 'Laila',
                'email_mhs' => '123456789019@mhs.dinus.ac.id',
                'password' => bcrypt('password'),
                'photo' => 'default.jpg',
                'ban_until' => null,
                'is_admin' => false,
            ],
            [
                'name' => 'Budi',
                'email_mhs' => '123456789020@mhs.dinus.ac.id',
                'password' => bcrypt('password'),
                'photo' => 'default.jpg',
                'ban_until' => null,
                'is_admin' => false,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
