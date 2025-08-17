<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowWithProductRequest extends FormRequest
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
            'slug' => 'nullable|string',
            'min_price' => 'nullable|integer',
            'max_price' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'page' => 'nullable',
            'count' => 'nullable',
            'category' => 'nullable|string',

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
            'slug.string' => 'شناسه دسته‌بندی باید متن باشد.',
            'min_price.integer' => 'حداقل قیمت باید عدد صحیح باشد.',
            'max_price.integer' => 'حداکثر قیمت باید عدد صحیح باشد.',
            'sort_by.string' => 'نوع مرتب‌سازی باید متن باشد.',
            'category.string' => 'دسته‌بندی باید متن باشد.',
        ];
    }
}
