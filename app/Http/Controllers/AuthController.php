<?php

namespace App\Http\Controllers;

use App\Services\ApiResponseService;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService){
        $this->authService = $authService;
    }

    public function login(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            $data = $this->authService->login($request->username, $request->password);
            return ApiResponseService::success($data, 'Congratulations! You have successfully logged in');
        } catch (\Exception $e) {
            return ApiResponseService::error((object) [], 'Login failed. Invalid username or password.', 401);
        }
    }

    public function logout (Request $request) {
        $this->authService->logout();
        return ApiResponseService::success((object) [], 'You have successfully logged out');
    }
}
