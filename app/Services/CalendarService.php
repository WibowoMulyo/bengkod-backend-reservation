<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

class CalendarService
{
    public function getWeeklyReservations($tableId)
    {
        $startOfCurrentWeek  = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfCurrentWeek = $startOfCurrentWeek->copy()->addDays(4);

        $startOfLastWeek = $startOfCurrentWeek->copy()->subWeek();
        $endOfLastWeek = $startOfLastWeek->copy()->addDays(4);

        $currentWeekReservationCount = Reservation::whereBetween('date', [$startOfCurrentWeek, $endOfCurrentWeek])->count();
        $lastWeekReservationCount = Reservation::whereBetween('date', [$startOfLastWeek, $endOfLastWeek])->count();

        $percentageChange = 0;
        if ($lastWeekReservationCount > 0) {
            $percentageChange = (($currentWeekReservationCount - $lastWeekReservationCount) / $lastWeekReservationCount) * 100;
        }

        $currentWeekQuery = Reservation::whereBetween('date', [$startOfCurrentWeek, $endOfCurrentWeek]);
        if ($tableId) {
            $currentWeekQuery->where('table_id', $tableId);
        }
        $currentWeekReservations = $currentWeekQuery->get(['code', 'table_id', 'time_slot', 'date']);

        $reservations = $currentWeekReservations->map(function ($reservation) {
            return [
                'code' => $reservation->code,
                'table_id' => $reservation->table_id,
                'time_slot' => $reservation->time_slot,
                'date' => $reservation->date->format('Y-m-d'),
            ];
        });

        return [
            'total_reservations' => $currentWeekReservationCount,
            'percentage_change' => $percentageChange . '%',
            'reservations' => $reservations,
        ];
    }
}
