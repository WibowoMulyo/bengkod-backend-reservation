<?php

namespace App\Http\Controllers;

use App\Services\TableService;
use Illuminate\Http\Request;

class TableController extends Controller
{
    protected $tableService;

    public function __construct(TableService $tableService)
    {
        $this->tableService = $tableService;
    }

    public function index()
    {
        $tables = $this->tableService->getAllTable();
        return response()->json($tables);
    }

    public function store(Request $request)
    {
        $table = $this->tableService->createTable($request);
        return response()->json(['message' => 'Table created successfully', 'data' => $table], 201);
    }


    public function show($id)
    {
        $table = $this->tableService->getTableById($id);
        return response()->json($table);
    }

    public function update(Request $request, $id)
    {
        $table = $this->tableService->updateTable($request, $id);
        return response()->json(['message' => 'Table updated successfully', 'data' => $table]);
    }

    public function destroy($id)
    {
        $this->tableService->destroy($id);
        return response()->json(['message' => 'Table deleted successfully']);
    }
}
