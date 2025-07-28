<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="UpdateWishlistRequest",
 *   type="object",
 *   @OA\Property(property="name", type="string", example="لیست علاقه‌مندی‌ها"),
 * )
 */
class UpdateWishlistRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'string',
        ];
    }
} 