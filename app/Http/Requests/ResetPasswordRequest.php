<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="ResetPasswordRequest",
 *   type="object",
 *   title="Reset Password Request",
 *   description="Request body for resetting user password",
 *   required={"email","code","password","password_confirmation"},
 *   @OA\Property(property="email", type="string", format="email", example="ali@example.com"),
 *   @OA\Property(property="code", type="string", example="1234"),
 *   @OA\Property(property="password", type="string", minLength=6, example="newpassword123"),
 *   @OA\Property(property="password_confirmation", type="string", minLength=6, example="newpassword123")
 * )
 */
class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'code' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
} 