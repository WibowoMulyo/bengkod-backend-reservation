<?php

namespace App\Services;

use App\Models\Table;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TableService
{
    public function getAllTable()
    {
        return Table::all();
    }

    public function getTableById($id)
    {
        $table = Table::findOrFail($id);
        if (!$table) {
            throw new ModelNotFoundException("Table with ID $id not found.");
        }

        return $table;
    }

    public function createTable($totalSeats, $tableNumber, $thumbnail, $isAvailable)
    {
        Table::create([
            'total_seats' => $totalSeats,
            'table_number' => $tableNumber,
            'thumbnail' => $thumbnail,
            'is_available' => $isAvailable,
        ]);

        return (object)[];
    }

    public function updateTable($id, array $data)
    {
        $table = $this->getTableById($id);
        $table->update($data);

        return (object)[];
    }

    public function destroy($id)
    {
        $table = $this->getTableById($id);
        $table->delete();
    }
}
