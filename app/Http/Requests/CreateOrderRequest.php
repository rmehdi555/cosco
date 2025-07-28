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
 *             required={"product_id", "quantity"},
 *             @OA\Property(
 *                 property="product_id",
 *                 type="integer",
 *                 description="Product ID",
 *                 example=1
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
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:100000',
            'shipping_address_id' => 'required|integer|exists:addresses,id',
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
            'items.*.product_id.required' => __('orders.product_id_required'),
            'items.*.product_id.integer' => __('orders.product_id_must_be_integer'),
            'items.*.product_id.exists' => __('orders.product_not_found'),
            'items.*.quantity.required' => __('orders.quantity_required'),
            'items.*.quantity.integer' => __('orders.quantity_must_be_integer'),
            'items.*.quantity.min' => __('orders.quantity_min_one'),
            'items.*.quantity.max' => __('orders.quantity_max_limit'),
            'shipping_address_id.required' => __('orders.shipping_address_required'),
            'shipping_address_id.integer' => __('orders.shipping_address_must_be_integer'),
            'shipping_address_id.exists' => __('orders.shipping_address_not_found'),
        ];
    }
} 