<?php

namespace App\Http\Controllers;

use App\Services\ApiResponseService;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService){
        $this->authService = $authService;
    }

    public function register(Request $request) {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email_mhs' => [
                    'required',
                    'email',
                    'unique:users,email_mhs',
                    function ($attribute, $value, $fail) {
                        if (!str_ends_with($value, '@mhs.dinus.ac.id')) {
                            $fail('Oops! Email harus menggunakan domain @mhs.dinus.ac.id.');
                        }

                        $localPart = substr($value, 0, strrpos($value, '@'));
                        if (strlen($localPart) != 12) {
                            $fail('Pastikan bagian sebelum @ memiliki tepat 12 karakter, ya!');
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

            return ApiResponseService::success($response, 'Selamat! Registrasi Anda berhasil.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseService::error($e->errors(), 'Oops! Ada beberapa kesalahan pada data Anda. Silakan cek lagi.', 422);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return ApiResponseService::error(null, $e->getMessage(), 401);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'Maaf, terjadi kesalahan. Silakan coba lagi nanti!', 500);
        }
    }

    public function login(Request $request) {
        try {
            $request->validate([
                'email_mhs' => 'required|email',
                'password' => 'required|string|min:8'
            ]);

            $data = $this->authService->login($request->email_mhs, $request->password);
            return ApiResponseService::success($data, 'Selamat datang kembali! Anda berhasil masuk.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseService::error($e->errors(), 'Ups! Ada beberapa kesalahan saat validasi data. Silakan periksa lagi.', 422);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return ApiResponseService::error(null, 'Hmm, email atau password Anda tidak cocok. Coba lagi, ya!', 401);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'Maaf, terjadi kesalahan tak terduga. Coba lagi nanti!', 500);
        }
    }

    public function logout (Request $request) {
        $this->authService->logout();
        return ApiResponseService::success((object) [], 'You have successfully logged out');
    }
}
