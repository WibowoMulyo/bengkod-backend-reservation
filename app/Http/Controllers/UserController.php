<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show()
    {
        try{
            $userId = Auth::id(); // Mendapatkan ID user yang sedang login
            $user = $this->userService->getUserData($userId);
            return ApiResponseService::success($user, 'User data retrieved successfully', 200);
        }catch (\Exception $e){
            return ApiResponseService::error(null, 'Failed retrieve user data', 400);
        }
    }

    public function update(Request $request)
    {
        try{
            $userId = Auth::id();
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email_mhs' => 'sometimes||string|email|unique:users,email,' . $userId,
                'password' => 'sometimes|string|min:6|confirmed',
                'photo' => 'sometimes|string',
            ]);

            if (isset($validatedData['password'])) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            }
    
            $user = $this->userService->updateUserData($userId, $validatedData);
            return ApiResponseService::success($user, 'User data updated successfully', 200);
        }catch (\Exception $e){
            return ApiResponseService::error(null, 'Failed to update user data', 400);
        }
    }
}