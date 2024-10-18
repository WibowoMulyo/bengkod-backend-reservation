<?php

namespace App\Http\Controllers;

use App\Services\ApiResponseService;
use App\Services\TableService;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            return ApiResponseService::success($tables, 'Table retrieved successfully', 200);
        } catch (QueryException $e){
            Log::error('Database error while retrieving tables: ' . $e->getMessage());
            return ApiResponseService::error((object) [], 'Failed to load table data. Please try again later.', 500);
        } catch (\Exception $e) {
            Log::error('Error retrieving tables: ' . $e->getMessage());
            return ApiResponseService::error((object) [], 'Something went wrong. Please try again.', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'total_seats' => 'required|integer|min:1',
                'table_number' => 'required|string|unique:tables,table_number',
                'thumbnail' => 'nullable|string',
                'is_available' => 'required|boolean',
            ]);

            $table = $this->tableService->createTable(
                $request->total_seats,
                $request->table_number,
                $request->thumbnail,
                $request->is_available
            );

            return ApiResponseService::success($table, 'Table created successfully', 201);
        } catch (ValidationException $e) {
            return ApiResponseService::error($e->getMessage(), 'Validation Error', 422);
        } catch (QueryException $e) {
            return ApiResponseService::error([], 'Database Error: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            return ApiResponseService::error([], 'Failed to create table: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try{
            $table = $this->tableService->getTableById($id);
            return ApiResponseService::success($table, 'Table by id retrieved successfully', 200);
        } catch (QueryException $e){
            Log::error('Database error while retrieving tables: ' . $e->getMessage());
            return ApiResponseService::error((object) [], 'Failed to load table data. Please try again later.', 500);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error((object)[], 'Table not found with ID ' . $id, 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving tables: ' . $e->getMessage());
            return ApiResponseService::error((object) [], 'Something went wrong. Please try again.', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $validatedData = $request->validate([
                'total_seats' => 'sometimes|integer|min:1',
                'table_number' => 'sometimes|string|unique:tables,table_number,' . $id,
                'thumbnail' => 'nullable|string',
                'is_available' => 'required|boolean',
            ]);
            $table = $this->tableService->updateTable($id,$validatedData);

            return ApiResponseService::success($table,'Table updated successfully', 200);
        } catch (ValidationException $e) {
            return ApiResponseService::error((object)[], 'Validation error: ' . $e->getMessage(), 422);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error((object)[], 'Table not found with ID ' . $id, 404);
        } catch (\Exception $e) {
            return ApiResponseService::error((object)[], 'Failed to update table: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try{
            $this->tableService->destroy($id);
            return ApiResponseService::success((object) [],'Table deleted successfully', 200);
        } catch (ModelNotFoundException $e){
            return ApiResponseService::error((object) [], 'Table not found with ID ' . $id, 404);
        } catch (\Exception $e){
            return ApiResponseService::error((object) [], 'Failed to delete table', 500);
        }
    }
}
