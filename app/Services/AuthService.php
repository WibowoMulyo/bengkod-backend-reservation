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

    public function __construct() {
        $this->apiUrl = "https://api.dinus.ac.id/api/v1/siadin/login";
    }

    public function register($name, $emailMhs, $password, $passwordConf) {
        if ($password != $passwordConf) {
            throw new AuthenticationException('Password dan konfirmasi password tidak sesuai.');
        }

        $existingUser = User::where('email_mhs', $emailMhs)->first();
        if ($existingUser) {
            throw new AuthenticationException('Email sudah terdaftar.');
        }

        User::create([
            'name' => $name,
            'email_mhs' => $emailMhs,
            'password' => bcrypt($password),
            'photo' => 'default.jpg',
            'is_admin' => false,
            'reservation_tokens' => 4,
        ]);

        return (object)[];
    }

    public function login($emailMhs, $password) {
        $user = User::where('email_mhs', $emailMhs)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new AuthenticationException('Email atau password salah.');
        }

        $token = JWTAuth::fromUser($user);

        return ['token' => $token];
    }

    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
