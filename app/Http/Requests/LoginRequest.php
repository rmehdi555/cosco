<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="LoginRequest",
 *   type="object",
 *   title="Login Request",
 *   description="Request body for user login",
 *   required={"email","password"},
 *   @OA\Property(property="email", type="string", format="email", example="info@cosco.com"),
 *   @OA\Property(property="password", type="string", example="aA123456"),
 * )
 */
class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }
} 