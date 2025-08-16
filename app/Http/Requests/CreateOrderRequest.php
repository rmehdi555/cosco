<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="CreateOrderRequest",
 *     title="Create Order Request",
 *     description="Request schema for creating a new order",
 *     required={"items", "shipping_address_id"},
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="Array of order items",
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
 *     ),
 *     @OA\Property(
 *         property="shipping_address_id",
 *         type="integer",
 *         description="Shipping address ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Order description",
 *         nullable=true,
 *         example="توضیحات سفارش"
 *     )
 * )
 */
class CreateOrderRequest extends FormRequest
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
            'shipping_address_id' => 'required|integer|exists:addresses,id',
            'description' => 'nullable|string|max:1000',
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
            'items.required' => __('orders.items_required'),
            'items.array' => __('orders.items_must_be_array'),
            'items.min' => __('orders.items_min_one'),
            'items.*.product_slug.required' => __('orders.product_slug_required'),
            'items.*.product_slug.string' => __('orders.product_slug_must_be_string'),
            'items.*.product_slug.exists' => __('orders.product_not_found'),
            'items.*.quantity.required' => __('orders.quantity_required'),
            'items.*.quantity.integer' => __('orders.quantity_must_be_integer'),
            'items.*.quantity.min' => __('orders.quantity_min_one'),
            'items.*.quantity.max' => __('orders.quantity_max_limit'),
            'shipping_address_id.required' => __('orders.shipping_address_required'),
            'shipping_address_id.integer' => __('orders.shipping_address_must_be_integer'),
            'shipping_address_id.exists' => __('orders.shipping_address_not_found'),
            'description.string' => __('orders.description_must_be_string'),
            'description.max' => __('orders.description_max_length'),
        ];
    }
} 