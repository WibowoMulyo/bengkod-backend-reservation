<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            $userId = Auth::id();
            $user = $this->userService->getUserData($userId);
            return ApiResponseService::success($user, 'User data retrieved successfully', 200);
        }catch (\Exception $e){
            return ApiResponseService::error(null, 'Failed retrieve user data', 400);
        }
    }
    public function showUserProfile()
    {
        try{
            $userId = Auth::id();
            $user = $this->userService->getUserProfile($userId);
            return ApiResponseService::success($user, 'User data retrieved successfully', 200);
        }catch (\Exception $e){
            return ApiResponseService::error(null, 'Failed retrieve user data', 400);
        }
    }
    public function update(UpdateUserRequest $request)
    {
        try{
            $validatedData = $request->validated();

            $user = $this->userService->updateUserData($validatedData);

            return ApiResponseService::success($user, 'User data updated successfully', 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseService::error($e->errors(), 'Validation failed', 422);
                
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'Failed to update user data: ' . $e->getMessage(), 400);
        }
    }
}
