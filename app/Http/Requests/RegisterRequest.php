<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="RegisterRequest",
 *   type="object",
 *   title="Register Request",
 *   description="Request body for user registration",
 *   required={"cell_phone"},
 *   @OA\Property(property="first_name", type="string", maxLength=255, example="علی"),
 *   @OA\Property(property="last_name", type="string", maxLength=255, example="احمدی"),
 *   @OA\Property(property="email", type="string", format="email", maxLength=255, example="ali@example.com", nullable=true, description="If not provided, will be set to cell_phone@mail.com"),
 *   @OA\Property(property="cell_phone", type="string", example="09123456789"),
 *   @OA\Property(property="password", type="string", minLength=6, example="password123", nullable=true, description="If not provided, will be set to cell_phone"),
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
            'email' => 'nullable|string|email|max:255|unique:users',
            'cell_phone' => 'required|string',
            'password' => 'nullable|string|min:6',
        ];
    }

    public function messages()
    {
        return [
            'first_name.string' => __('validation.first_name_string'),
            'first_name.max' => __('validation.first_name_max'),
            'last_name.string' => __('validation.last_name_string'),
            'last_name.max' => __('validation.last_name_max'),
            'email.required' => __('validation.email.required'),
            'email.string' => __('validation.email.string'),
            'email.email' => __('validation.email.email'),
            'email.max' => __('validation.email.max'),
            'email.unique' => __('validation.email.unique'),
            'cell_phone.required' => __('validation.cell_phone.required'),
            'cell_phone.string' => __('validation.cell_phone.string'),
            'password.required' => __('validation.password.required'),
            'password.string' => __('validation.password_string'),
            'password.min' => __('validation.password.min'),
        ];
    }
} 