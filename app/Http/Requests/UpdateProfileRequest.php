<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'avatar_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'password' => 'sometimes|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|string|min:8',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'first_name.string' => trans('validation.first_name_string'),
            'first_name.max' => trans('validation.first_name_max'),
            'last_name.string' => trans('validation.last_name_string'),
            'last_name.max' => trans('validation.last_name_max'),
            'avatar_image.image' => trans('validation.avatar_image_image'),
            'avatar_image.mimes' => trans('validation.avatar_image_mimes'),
            'avatar_image.max' => trans('validation.avatar_image_max'),
            'password.string' => trans('validation.password_string'),
            'password.min' => trans('validation.password_min'),
            'password.confirmed' => trans('validation.password_confirmed'),
            'password_confirmation.required_with' => trans('validation.password_confirmation_required_with'),
            'password_confirmation.string' => trans('validation.password_confirmation_string'),
            'password_confirmation.min' => trans('validation.password_confirmation_min'),
        ];
    }
} 