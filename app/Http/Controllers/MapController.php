<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetMapRequest;
use App\Services\ApiResponseService;
use App\Services\MapService;

class MapController extends Controller
{
    protected $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    public function getAvailableTables(GetMapRequest $request)
    {
        try{
            $validated = $request->validated();
            $totalSeats = $validated['total_seats'] ?? 1;
            $tables = $this->mapService->getAvailableTables( $validated['type'],  $validated['date'], $totalSeats);
            return ApiResponseService::success($tables, 'Berhasil mengambil data meja', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseService::error($e->errors(), 'Validasi gagal', 422);
        } catch (\Exception $e) {
            return ApiResponseService::error((object)[], 'Gagal mengambil data meja: ' . $e->getMessage(), 400);
        }
    }

    public function getTableDetails(GetMapRequest $request)
    {
        try{
            $validated = $request->validated();
            $table = $this->mapService->getTableDetails($validated['tableId'], $validated['date']);
            return ApiResponseService::success($table, 'Berhasil mengambil data meja', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseService::error($e->errors(), 'Validasi gagal', 422);
        } catch (\Exception $e) {
            return ApiResponseService::error((object)[], 'Gagal mengambil data meja: ' . $e->getMessage(), 400);
        }
    }
}
