<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="LoginRequest",
 *   type="object",
 *   title="Login Request",
 *   description="Request body for user login",
 *   required={"password"},
 *   @OA\Property(property="email", type="string", format="email", example="info@cosco.com", nullable=true, description="User's email address"),
 *   @OA\Property(property="cell_phone", type="string", example="09123456789", nullable=true, description="User's cell phone number"),
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
            'email' => 'nullable|email',
            'cell_phone' => 'nullable|string',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.email' => __('validation.email_format'),
            'password.required' => __('validation.password_required'),
            'password.string' => __('validation.password_string'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->email && !$this->cell_phone) {
                $validator->errors()->add('identifier', __('validation.identifier_required'));
            }
        });
    }
}
