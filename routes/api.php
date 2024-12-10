<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ReservationController;
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

/* General Routes*/
/*----------------------------------- AUTH -----------------------------------*/
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

/*----------------------------------- DASHBOARD USER PROFILE -----------------------------------*/
Route::get('/user-profile', [UserController::class, 'showUserProfile'])->middleware('auth:api');

/*----------------------------------- DASHBOARD CALENDAR -----------------------------------*/
Route::get('/calendar', [CalendarController::class, 'getWeeklyReservations'])->middleware('auth:api');


/* User Routes */
/*----------------------------------- AUTH -----------------------------------*/
Route::post('/register', [AuthController::class, 'register']);

/*----------------------------------- USER PROFILE -----------------------------------*/
Route::get('/get-user', [UserController::class, 'showUser']);
Route::patch('/update-user',[UserController::class, 'updateUserProfile']);

/*----------------------------------- RESERVATION -----------------------------------*/
Route::post('/reservations', [ReservationController::class, 'store'])->middleware('auth:api');
Route::get('/reservations', [ReservationController::class, 'checkStatus'])->middleware('auth:api');
Route::get('/reservations/confirm-presence', [ReservationController::class, 'confirmPresence'])->middleware('auth:api');
Route::get('/reservations/confirm-team/{reservationId}/{userId}', [ReservationController::class, 'confirmTeam'])->name('reservations.confirmTeam');
Route::get('/map', [MapController::class, 'getAvailableTables'])->middleware('auth:api');
Route::get('/detail-reservation-table', [MapController::class, 'getTableDetails'])->middleware('auth:api');


/* Admin Routes */
/*----------------------------------- MANAGE TABLE -----------------------------------*/
Route::apiResource('table', TableController::class)->middleware(['auth:api', 'admin']);