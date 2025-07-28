<?php

namespace App\Http\Resources;

/**
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     title="Error Response",
 *     description="Standard error response schema",
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(property="errors", type="object", example={}),
 *     @OA\Property(property="status", type="integer", example=400)
 * )
 */
class SwaggerSchemas
{
    // This class is used only for Swagger documentation
    // No actual functionality needed
} 