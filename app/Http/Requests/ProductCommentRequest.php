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
}
