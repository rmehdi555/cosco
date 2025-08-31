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
            'min_price' => 'nullable|integer|min:0',
            'max_price' => 'nullable|integer|min:0',
            'sort_by' => 'nullable|string|in:cheapest,expensive,newest',
            'page' => 'nullable|integer|min:1',
            'count' => 'nullable|integer|min:1|max:100',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
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
            'min_price.min' => 'حداقل قیمت نمی‌تواند منفی باشد.',
            'max_price.integer' => 'حداکثر قیمت باید عدد صحیح باشد.',
            'max_price.min' => 'حداکثر قیمت نمی‌تواند منفی باشد.',
            'sort_by.string' => 'نوع مرتب‌سازی باید متن باشد.',
            'sort_by.in' => 'نوع مرتب‌سازی باید یکی از مقادیر ارزان‌ترین، گران‌ترین یا جدیدترین باشد.',
            'page.integer' => 'شماره صفحه باید عدد صحیح باشد.',
            'page.min' => 'شماره صفحه باید حداقل 1 باشد.',
            'count.integer' => 'تعداد نتایج باید عدد صحیح باشد.',
            'count.min' => 'تعداد نتایج باید حداقل 1 باشد.',
            'count.max' => 'تعداد نتایج نمی‌تواند بیشتر از 100 باشد.',
            'category.string' => 'دسته‌بندی باید متن باشد.',
            'brand.string' => 'برند باید متن باشد.',
            'rating.integer' => 'امتیاز باید عدد صحیح باشد.',
            'rating.min' => 'امتیاز باید حداقل 1 باشد.',
            'rating.max' => 'امتیاز نمی‌تواند بیشتر از 5 باشد.',
        ];
    }
}
