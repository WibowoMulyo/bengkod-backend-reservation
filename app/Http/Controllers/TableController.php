<?php

namespace App\Http\Controllers;

use App\Services\ApiResponseService;
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
        try{
            $tables = $this->tableService->getAllTable();
            return ApiResponseService::success($tables, 'Table retrieved successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseService::error((object) [], 'Failed to retrieve table', 401);
        }
    }

    public function store(Request $request)
    {
        try{
            $table = $this->tableService->createTable($request);
            return ApiResponseService::success($table, 'Table created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseService::error((object) [], 'Failed to create table', 401);
        }
    }

    public function show($id)
    {
        try{
            $table = $this->tableService->getTableById($id);
            return ApiResponseService::success($table, 'Table by id retrieved successfully', 201);
        } catch (\Exception $e){
            return ApiResponseService::error((object) [], 'Failed to retrieve table by id', 401);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $table = $this->tableService->updateTable($request, $id);
            return ApiResponseService::success($table,'Table updated successfully', 201);
        } catch (\Exception $e){
            return ApiResponseService::error((object) [], 'Failed to update table', 401);
        }
    }

    public function destroy($id)
    {
        try{
            $this->tableService->destroy($id);
            return ApiResponseService::success([],'Table deleted successfully', 201);
        } catch (\Exception $e){
            return ApiResponseService::error((object) [], 'Failed to delete table', 401);
        }
    }
}
