<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
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
        $response = Http::post($this->apiUrl, [
            'username' => $username,
            'password' => $password
        ]);

        if ($response->ok()) {
            $data = $response->json();
            $accessToken = $data['data']['access_token'];

            $profileResponse = Http::withHeaders(['Authorization' => 'Bearer ' . $accessToken])
                ->get('https://api.dinus.ac.id/api/v1/siadin/profile');

            $profileData = $profileResponse->json();
            $email = $profileData['data']['email_mhs'];
            $name = $profileData['data']['nama'];
            $photo = $profileData['data']['photo'];

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(Str::random(16)),
                    'is_admin' => false,
                    'photo' => $photo,
                    'reservation_tokens' => 4,
                ]);
            }

            $token = JWTAuth::fromUser($user);

            return [
                'token' => $token,
            ];
        }
    }

    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
