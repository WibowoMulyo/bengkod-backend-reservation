<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        try{
            $userId = Auth::id();
            $user = $this->userService->getUserData($userId);
            return ApiResponseService::success($user, 'Berhasil mengambil data user', 200);
        }catch (\Exception $e){
            return ApiResponseService::error((object)[], 'Gagal mengambil data user', 400);
        }
    }
    public function show()
    {
        try{
            $userId = Auth::id();
            $user = $this->userService->getUserProfile($userId);
            return ApiResponseService::success($user, 'Berhasil mengambil data user', 200);
        }catch (\Exception $e){
            return ApiResponseService::error((object)[], 'Gagal mengambil data user', 400);
        }
    }
    public function update(UpdateUserRequest $request)
    {
        try{
            $validatedData = $request->validated();
            $userIdFromUrl = $request->route('user');
            $user = $this->userService->updateUserData($userIdFromUrl, $validatedData);
            return ApiResponseService::success($user, 'Berhasil mengupdate data user', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseService::error($e->errors(), 'Validasi gagal', 422);
        } catch (\Exception $e) {
            return ApiResponseService::error((object)[], 'Gagal mengupdate data user: ' . $e->getMessage(), 400);
        }
    }
    public function destroy($id)
    {
        try{
            $this->userService->destroy($id);
            return ApiResponseService::success((object) [],'Berhasil menghapus data photo pada user', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error((object)[], 'Gagal menghapus data photo pada user: ' . $e->getMessage(), 400);
        }
    }
}
