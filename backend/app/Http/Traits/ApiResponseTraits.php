<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTraits
{
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'status_code' => $statusCode,
        ], $statusCode);
    }

    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'status_code' => $statusCode,
        ], $statusCode);
    }
}
