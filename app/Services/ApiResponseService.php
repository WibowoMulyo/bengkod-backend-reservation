<?php

namespace App\Services;

class ApiResponseService {
    public static function success($data = [], $message = 'Success', $code = 200){
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    public static function error($data = [], $message = 'Error', $code = 500){
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
