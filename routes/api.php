<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

<<<<<<< HEAD
=======
Route::post('/login', action: [AuthController::class, 'login']);
Route::post('/logout', action: [AuthController::class, 'logout']);

>>>>>>> parent of 5b37b45 (Fixed login endpoint)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
