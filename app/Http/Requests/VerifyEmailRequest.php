<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="VerifyEmailRequest",
 *   type="object",
 *   title="Verify Email Request",
 *   description="Request body for verifying user email",
 *   required={"email","code"},
 *   @OA\Property(property="email", type="string", format="email", example="ali@example.com"),
 *   @OA\Property(property="code", type="string", example="1234"),
 * )
 */
class VerifyEmailRequest extends FormRequest
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
        ];
    }
} 