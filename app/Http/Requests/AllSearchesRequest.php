<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllSearchesRequest extends FormRequest
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
            'q' => 'required|string',
            'page' => 'nullable',
            'count' => 'nullable',
            'sort_by' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'q.required' => 'عبارت جستجو الزامی است.',
            'q.string' => 'عبارت جستجو باید متن باشد.',
            'sort_by.string' => 'نوع مرتب‌سازی باید متن باشد.',
        ];
    }
}
