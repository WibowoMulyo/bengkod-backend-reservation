<?php

namespace App\Http\Controllers;

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
        $tableId = $request->input("table_id");
        $reservations = $this->calendarService->getWeeklyReservations($tableId);
        return response()->json($reservations);
    }
}
