<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CartItemResource",
 *     title="Cart Item Resource",
 *     description="Cart item resource schema",
 *     @OA\Property(property="quantity", type="integer", example=2, description="Quantity of the product in cart"),
 *     @OA\Property(property="description", type="string", example="توضیحات اضافی", description="Additional description for cart item"),
 *     @OA\Property(property="price", type="number", format="decimal", example=75000, description="Current price of the product"),
 *     @OA\Property(property="old_price", type="number", format="decimal", example=70000, description="Price when item was added to cart"),
 *     @OA\Property(property="total_price", type="number", format="decimal", example=150000, description="Total price (quantity × current price)"),
 *     @OA\Property(property="product_name", type="string", example="محصول نمونه", description="Product name"),
 *     @OA\Property(property="product_id", type="integer", example=1, description="Product ID"),
 *     @OA\Property(property="product_image", type="string", example="https://example.com/image.jpg", description="Product image URL"),
 *     @OA\Property(property="product_slug", type="string", example="sample-product", description="Product slug for URL"),
 *     @OA\Property(property="product_code", type="string", example="PRD-001", description="کد یکتای محصول"),
 *     @OA\Property(property="count_for_user", type="integer", example=10, description="Count of the product for the user")
 * )
 */
class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'quantity' => $this->quantity,
            'description' => $this->description ?? '',
            'price' => config('general.show_price')($this->price),
            'total_price' => config('general.show_price')($this->total_price),
            'product_id' => $this->product?->id,
            'product_name' => $this->product?->name,
            'product_image' => $this->product?->images?->first()?->image_url ? asset('storage/' . $this->product->images->first()->image_url) : '',
            'product_slug' => $this->product?->slug,
            'product_code' => $this->product?->code,
            'count_for_user' => $this->product?->stock,
        ];
    }
}
