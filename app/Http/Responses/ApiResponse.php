<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, $message = null, $status = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message ?? __('messages.success', [], 'fa'),
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    public static function error($message = null, $errors = null, $status = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message ?? __('errors.server_error', [], 'fa'),
            'data' => null,
            'errors' => $errors,
        ], $status);
    }

    public static function notFound($message = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message ?? __('errors.not_found', [], 'fa'),
            'data' => null,
            'errors' => null,
        ], 404);
    }
} 