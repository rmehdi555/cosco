<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="ForgotPasswordRequest",
 *   type="object",
 *   title="Forgot Password Request",
 *   description="Request body for requesting password reset",
 *   required={"email"},
 *   @OA\Property(property="email", type="string", format="email", example="ali@example.com")
 * )
 */
class ForgotPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }
}
