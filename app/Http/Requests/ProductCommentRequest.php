<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCommentRequest extends FormRequest
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
            'rate' => 'required|integer|between:1,5',
            'description' => 'nullable|string',
            'product_slug' => 'required|string',
            'comment' => 'nullable|array',
            'comment.*.file' => 'nullable|file|mimes:jpeg,png,jpg|max:5000',
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
            'rate.required' => 'امتیاز محصول الزامی است.',
            'rate.integer' => 'امتیاز باید عدد صحیح باشد.',
            'rate.between' => 'امتیاز باید بین 1 تا 5 باشد.',
            'description.string' => 'توضیحات باید متن باشد.',
            'product_slug.required' => 'شناسه محصول الزامی است.',
            'product_slug.string' => 'شناسه محصول باید متن باشد.',
            'comment.array' => 'فایل‌ها باید به صورت آرایه ارسال شوند.',
            'comment.*.file.file' => 'فایل انتخاب شده معتبر نیست.',
            'comment.*.file.mimes' => 'فقط فایل‌های jpeg، png و jpg قابل قبول هستند.',
            'comment.*.file.max' => 'حجم فایل نباید بیشتر از 5 مگابایت باشد.',
        ];
    }
}
