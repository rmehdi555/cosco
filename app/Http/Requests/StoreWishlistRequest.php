<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="StoreWishlistRequest",
 *   type="object",
 *   required={"name"},
 *   @OA\Property(property="name", type="string", example="لیست علاقه‌مندی‌ها"),
 * )
 */
class StoreWishlistRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'required|string',
        ];
    }
}
