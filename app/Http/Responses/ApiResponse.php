<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, $message = null, $status = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message ?? __('messages.success', [], 'fa'),
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    public static function error($message = null, $errors = null, $status = 400): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message ?? __('errors.server_error', [], 'fa'),
            'data' => null,
            'errors' => $errors,
        ], $status);
    }

    public static function notFound($message = null): JsonResponse
    {
        return response()->json([
            'status' => 404,
            'message' => $message ?? __('errors.not_found', [], 'fa'),
            'data' => null,
            'errors' => null,
        ], 404);
    }

    public static function validationError($errors, $message = null): JsonResponse
    {
        return response()->json([
            'status' => 422,
            'message' => $message ?? __('errors.validation_failed', [], 'fa'),
            'data' => null,
            'errors' => $errors,
        ], 422);
    }

    public static function serverError($message = null, $error = null): JsonResponse
    {
        return response()->json([
            'status' => 500,
            'message' => $message ?? __('errors.server_error', [], 'fa'),
            'data' => null,
            'errors' => $error,
        ], 500);
    }
} 