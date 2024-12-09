<?php

namespace App\Services;

use App\Models\Table;
use Carbon\Carbon;
use Exception;

class MapService
{
    public function getAvailableTables(string $type, $date, int $totalSeats)
    {
        $lastDayLimit = Carbon::now()->addDay(3)->toDateString();
        $inputDate = Carbon::parse($date);
        $limitDate = Carbon::parse($lastDayLimit);

        if ($inputDate->lessThan(Carbon::now()->subDay(1))) {
            throw new Exception("Input tanggal tidak boleh kurang dari hari ini");
        }

        if ($inputDate->greaterThan($limitDate)) {
            throw new Exception("Input tanggal tidak boleh lebih dari 3 hari, dari hari ini");
        }
        
        return Table::where('type', $type)
        ->where('total_seats', '>=', $totalSeats)
        ->select('id', 'table_number', 'is_available')
        ->get();
    }

    public function getTableDetails(int $tableId, string $date)
    {
        $table = Table::where('id', $tableId)
            ->with(['reservations' => function($query) use ($date) {
                $query->whereDate('date', $date);
            }])
            ->first();

        return [
            'table' => [
                'id' => $table->id,
                'name' => $table->table_number,
                'thumbnail' => $table->thumbnail,
            ],
            'reservations' => $table->reservations->map(function($reservation) {
                return [
                    'time_slot' => $reservation->time_slot,
                    'status' => 'reserved',
                ];
            })->toArray(),
        ];
    }
}
