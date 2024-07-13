<?php

use App\Models\User;
use Illuminate\Http\JsonResponse;

if (!function_exists('api_response')) {

    function api_response(bool $success, $data = null, $response_code = 200, $massage = null, $errors = null, $meta = null): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $massage,
            'data' => $data,
            'errors' => $errors,
        ], $response_code);
    }
}

