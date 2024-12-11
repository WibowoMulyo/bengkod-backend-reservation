<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthService {
    protected $apiUrl;

    public function register($name, $emailMhs, $password, $passwordConf) {
        if ($password !== $passwordConf) {
            throw new AuthenticationException('Password dan konfirmasi password tidak cocok. Silakan coba lagi!');
        }

        $existingUser = User::where('email_mhs', $emailMhs)->first();
        if ($existingUser) {
            throw new AuthenticationException('Email ini sudah terdaftar. Coba gunakan email lain, ya!');
        }

        User::create([
            'name' => $name,
            'email_mhs' => $emailMhs,
            'password' => bcrypt($password),
            'photo' => 'default.jpg',
            'penalty_count' => 0,
            'is_admin' => false,
        ]);

        return (object)[];
    }

    public function login($emailMhs, $password) {
        $user = $this->validateCredentials($emailMhs, $password);

        return $user;
    }

    public function validateCredentials($emailMhs, $password){
        $user = User::where('email_mhs', $emailMhs)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new AuthenticationException('Email atau password salah.');
        }

        $token = JWTAuth::fromUser($user);

        $response = $user->toArray();
        $response['token'] = $token;

        return $response;
    }

    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
