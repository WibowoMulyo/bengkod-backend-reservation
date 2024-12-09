<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Services\ApiResponseService;
use App\Services\TableService;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
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
            return ApiResponseService::success($tables, 'Berhasil mengambil data meja', 200);
        } catch (QueryException $e){
            Log::error('Database error ketika mengambil data meja: ' . $e->getMessage());
            return ApiResponseService::error((object) [], 'Gagal memuat data meja. Silakan coba lagi nanti.', 500);
        } catch (\Exception $e) {
            Log::error('Error mengambil data: ' . $e->getMessage());
            return ApiResponseService::error((object) [], 'Terjadi kesalahan. Silakan coba lagi.', 500);
        }
    }

    public function store(StoreTableRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $table = $this->tableService->createTable($validatedData);
            return ApiResponseService::success($table, 'Meja berhasil dibuat', 201);
        } catch (ValidationException $e) {
            return ApiResponseService::error($e->getMessage(), 'Validasi gagal', 422);
        } catch (QueryException $e) {
            return ApiResponseService::error([], 'Database Error: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            return ApiResponseService::error([], 'Gagal membuat meja: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try{
            $table = $this->tableService->getTableById($id);
            return ApiResponseService::success($table, 'Berhasil mengambil data meja berdasarkan id', 200);
        } catch (QueryException $e){
            Log::error('Database error ketika mengambil data meja: ' . $e->getMessage());
            return ApiResponseService::error((object) [], 'Gagal memuat data meja. Silakan coba lagi nanti.', 500);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error((object)[], 'Data meja dengan ID ' . $id . ' tidak ditemukan', 404);
        } catch (\Exception $e) {
            Log::error('Error mengambil data meja: ' . $e->getMessage());
            return ApiResponseService::error((object) [], 'Terjadi kesalahan. Silakan coba lagi.', 500);
        }
    }

    public function update(UpdateTableRequest $request, $id)
    {
        try{
            $validatedData = $request->validated();
            $table = $this->tableService->updateTable($id,$validatedData);
            return ApiResponseService::success($table,'Berhasil memperbarui data meja', 200);
        } catch (ValidationException $e) {
            return ApiResponseService::error((object)[], 'Validasi error: ' . $e->getMessage(), 422);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error((object)[], 'Data meja dengan ID ' . $id . ' tidak ditemukan', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error((object)[], 'Gagal memperbarui data meja: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try{
            $this->tableService->destroy($id);
            return ApiResponseService::success((object) [],'Berhasil menghapus data meja', 200);
        } catch (ModelNotFoundException $e){
            return ApiResponseService::error((object) [], 'Data meja dengan ID ' . $id . ' tidak ditemukan', 404);
        } catch (\Exception $e){
            return ApiResponseService::error((object) [], 'Gagal menghapus data meja', 500);
        }
    }
}
