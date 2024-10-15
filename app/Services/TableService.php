<?php

namespace App\Services;

use App\Models\Table;
use Illuminate\Http\Request;

class TableService
{
    public function getAllTable()
    {
        return Table::all();
    }

    public function getTableById($id)
    {
        return Table::findOrFail($id);
    }

    public function createTable(Request $request)
    {
        $request->validate([
            'total_seats' => 'required|integer|min:1',
            'table_number' => 'required|string|unique:tables,table_number',
            'thumbnail' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        return Table::create([
            'total_seats' => $request->total_seats,
            'table_number' => $request->table_number,
            'thumbnail' => $request->thumbnail,
            'is_available' => $request->is_available,
        ]);
    }

    public function updateTable(Request $request, string $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'total_seats' => 'sometimes|integer|min:1',
            'table_number' => 'sometimes|string|unique:tables,table_number,' . $id,
            'thumbnail' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        $table->update($request->only(['total_seats', 'table_number', 'thumbnail', 'is_available']));

        return $table;
    }

    public function destroy(string $id)
    {
        $table = Table::findOrFail($id);
        $table->delete();
        return response()->json([
            'message' => 'Table deleted successfully'
        ], 200);
    }
}