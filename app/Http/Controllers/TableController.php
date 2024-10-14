<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data table dengan urutan table_number
        $tables = Table::orderBy('table_number', 'asc')->get();

        // Mengembalikan response JSON dengan daftar tables
        return response()->json([
            'message' => 'Tables retrieved successfully',
            'data' => $tables
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'total_seats' => 'required|integer|min:1',
            'table_number' => 'required|integer|unique:tables,table_number',
            'thumbnail' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        $table = Table::create([
            'total_seats' => $request->total_seats,
            'table_number' => $request->table_number,
            'thumbnail' => $request->thumbnail,
            'is_available' => $request->is_available,
        ]);

        return response()->json([
            'message' => 'Table created successfully',
            'data' => $table
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'total_seats' => 'required|integer|min:1',
            'table_number' => 'required|integer|unique:tables,table_number,' . $id,
            'thumbnail' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        $table->update([
            'total_seats' => $request->total_seats,
            'table_number' => $request->table_number,
            'thumbnail' => $request->thumbnail,
            'is_available' => $request->is_available,
        ]);

        return response()->json([
            'message' => 'Table updated successfully',
            'data' => $table
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $table = Table::findOrFail($id);
        $table->delete();
        return response()->json([
            'message' => 'Table deleted successfully'
        ], 200);
    }
}
