<?php

namespace App\Services;

use App\Models\Table;

class MapService
{
    public function getAvailableTables(string $type, int $totalSeats)
    {
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
