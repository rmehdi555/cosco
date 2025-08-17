<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="CreateCartRequest",
 *     title="Create Cart Request",
 *     description="Request schema for creating a new cart",
 *     required={"items"},
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="Array of cart items",
 *         @OA\Items(
 *             type="object",
 *             required={"product_slug", "quantity"},
 *             @OA\Property(
 *                 property="product_slug",
 *                 type="string",
 *                 description="Product slug",
 *                 example="mhsol-aol"
 *             ),
 *             @OA\Property(
 *                 property="quantity",
 *                 type="integer",
 *                 description="Quantity of the product",
 *                 minimum=1,
 *                 maximum=100000,
 *                 example=2
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 description="Additional description for cart item",
 *                 example="توضیحات اضافی برای این آیتم"
 *             )
 *         )
 *     )
 * )
 */

class CreateCartRequest extends FormRequest
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
            'items' => 'nullable|array',
            'items.*.product_slug' => 'nullable|string|exists:products,slug',
            'items.*.quantity' => 'nullable|integer|min:1|max:100000',
            'items.*.description' => 'nullable|string|max:1000',
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
            'items.array' => __('cart.items_must_be_array'),
            'items.*.product_slug.required' => __('cart.product_slug_required'),
            'items.*.product_slug.string' => __('cart.product_slug_must_be_string'),
            'items.*.product_slug.exists' => __('cart.product_not_found'),
            'items.*.quantity.integer' => __('cart.quantity_must_be_integer'),
            'items.*.quantity.max' => __('cart.quantity_max_limit'),
            'items.*.description.string' => __('cart.description_must_be_string'),
            'items.*.description.max' => __('cart.description_max_limit'),
        ];
    }
}
