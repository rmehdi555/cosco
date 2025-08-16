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
            'items' => 'required|array|min:1',
            'items.*.product_slug' => 'required|string|exists:products,slug',
            'items.*.quantity' => 'required|integer|min:1|max:100000',
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
            'items.required' => __('cart.items_required'),
            'items.array' => __('cart.items_must_be_array'),
            'items.min' => __('cart.items_min_one'),
            'items.*.product_slug.required' => __('cart.product_slug_required'),
            'items.*.product_slug.string' => __('cart.product_slug_must_be_string'),
            'items.*.product_slug.exists' => __('cart.product_not_found'),
            'items.*.quantity.required' => __('cart.quantity_required'),
            'items.*.quantity.integer' => __('cart.quantity_must_be_integer'),
            'items.*.quantity.min' => __('cart.quantity_min_one'),
            'items.*.quantity.max' => __('cart.quantity_max_limit'),
        ];
    }
} 