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

    public function regsiter(Request $request) {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email_mhs' => [
                    'required',
                    'email',
                    'unique:users,email_mhs',
                    function ($attribute, $value, $fail) {
                        if (!str_ends_with($value, '@mhs.dinus.ac.id')) {
                            $fail('Email harus menggunakan domain @mhs.dinus.ac.id.');
                        }

                        $localPart = substr($value, 0, strrpos($value, '@'));
                        if (strlen($localPart) != 12) {
                            $fail('Bagian sebelum @ harus memiliki tepat 12 karakter.');
                        }
                    },
                ],
                'password' => 'required|string|min:8|confirmed',
            ]);

            $response = $this->authService->register(
                $request->name,
                $request->email_mhs,
                $request->password,
                $request->password_confirmation
            );

            return ApiResponseService::success($response, 'Congratulations! You have successfully registered.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseService::error($e->errors(), 'Validation failed', 422);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return ApiResponseService::error(null, $e->getMessage(), 401);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred during registration', 500);
        }
    }
    public function login(Request $request) {
        $request->validate([
            'email_mhs' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        try {
            $data = $this->authService->login($request->email_mhs, $request->password);
            return ApiResponseService::success($data, 'Congratulations! You have successfully logged in');
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return ApiResponseService::error(null, 'Login failed. Invalid email or password.', 401);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred during login', 500);
        }
    }

    public function logout (Request $request) {
        $this->authService->logout();
        return ApiResponseService::success((object) [], 'You have successfully logged out');
    }
}
