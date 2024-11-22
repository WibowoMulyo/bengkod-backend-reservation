<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TableController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::apiResource('table', TableController::class);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('user',UserController::class);
    Route::get('/user-profile', [UserController::class, 'show']);
    
    Route::get('/calendar', [CalendarController::class, 'getWeeklyReservations']);

    Route::get('/map', [MapController::class, 'getAvailableTables']);
    Route::get('/detail-reservation-table', [MapController::class, 'getTableDetails']);
});

