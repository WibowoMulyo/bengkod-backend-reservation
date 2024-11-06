<?php

namespace App\Http\Controllers;

use App\Services\ApiResponseService;
use App\Services\CalendarService;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function getWeeklyReservations(Request $request)
    {
        try{
            $tableId = $request->input("table_id", 1);
            $reservations = $this->calendarService->getWeeklyReservations($tableId);
            return ApiResponseService::success($reservations, 'Calender data fetched successfully', 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseService::error($e->errors(), 'Validation failed', 422);
                
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'Failed to fetch calender data: ' . $e->getMessage(), 400);
        }
    }
}
