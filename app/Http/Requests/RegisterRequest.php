<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="RegisterRequest",
 *   type="object",
 *   title="Register Request",
 *   description="Request body for user registration",
 *   required={"email","cell_phone","password"},
 *   @OA\Property(property="first_name", type="string", maxLength=255, example="علی"),
 *   @OA\Property(property="last_name", type="string", maxLength=255, example="احمدی"),
 *   @OA\Property(property="email", type="string", format="email", maxLength=255, example="ali@example.com"),
 *   @OA\Property(property="cell_phone", type="string", example="09123456789"),
 *   @OA\Property(property="password", type="string", minLength=6, example="password123"),
 * )
 */
class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cell_phone' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
        ];
    }
} 