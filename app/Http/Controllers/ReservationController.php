<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use App\Models\Reservation;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function store(CreateReservationRequest $request)
    {
        try {
            Log::info('Incoming reservation request data:', $request->all());
            $reservation = $this->reservationService->createReservation($request);
            return ApiResponseService::success($reservation, 'Reservasi berhasil dibuat.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = collect($e->errors())->flatten()->all();
            return ApiResponseService::error((object)[], 'Validasi gagal: ' . implode(' ', $errorMessages), 422);
        } catch (\Exception $e) {
            return ApiResponseService::error((object)[], 'Terjadi kesalahan saat membuat reservasi: ' . $e->getMessage(), 500);
        }
    }

    public function confirmTeam($reservationId, $userId, Request $request)
    {
        try {
            $page = $this->reservationService->updateConfirmTeam($reservationId, $userId, $request);
            return $page;
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'Terjadi kesalahan saat mengkonfirmasi reservasi: ' . $e->getMessage(), 500);
        }
    }

    public function confirmPresence(Request $request)
    {
        try {
            $this->reservationService->updateConfirmPresence($request);
            return ApiResponseService::success(null, 'Kehadiran berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'Terjadi kesalahan saat mengkonfirmasi kehadiran: ' . $e->getMessage(), 500);
        }
    }

    public function checkStatus(Request $request)
    {
        try {
            $code = $request->query('code'); // Ambil kode dari query string

            if (!$code) {
                return ApiResponseService::error((object) [], 'Reservation code is required', 400);
            }

            $reservation = $this->reservationService->getReservationByCode($code);

            if (!$reservation) {
                return ApiResponseService::error((object) [], 'Reservation not found', 404);
            }

            return ApiResponseService::success($reservation, 'Reservation status retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseService::error((object) [], $e->getMessage(), 500);
        }
    }
}
