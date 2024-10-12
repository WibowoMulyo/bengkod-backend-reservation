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

    public function login($username, $password) {
        $user = User::where('email', $username)->first();

        if ($user && $user->is_admin && Hash::check($password, $user->password)) {
            $token = JWTAuth::fromUser($user);
            return ['token' => $token];
        }

        $response = Http::post($this->apiUrl, [
            'username' => $username,
            'password' => $password
        ]);

        if ($response->failed()) {
            Log::error('Login API Udinus gagal', ['response' => $response->body()]);
            throw new AuthenticationException('Login ke API Udinus gagal.');
        }

        $data = $response->json();
        $accessToken = $data['data']['access_token'];

        $profileResponse = Http::withHeaders(['Authorization' => 'Bearer ' . $accessToken])
            ->get('https://api.dinus.ac.id/api/v1/siadin/profile');

        if ($profileResponse->failed()) {
            Log::error('Pengambilan profil gagal', ['response' => $profileResponse->body()]);
            throw new AuthenticationException('Gagal mengambil profil mahasiswa.');
        }

        $profileData = $profileResponse->json();
        $email = $profileData['data']['email_mhs'];
        $name = $profileData['data']['nama'];
        $photo = $profileData['data']['photo'];

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email,
                'password' => bcrypt(Str::random(16)),
                'is_admin' => false,
                'photo' => $photo,
                'reservation_tokens' => 4,
            ]
        );

        $token = JWTAuth::fromUser($user);

        return ['token' => $token];
    }

    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
