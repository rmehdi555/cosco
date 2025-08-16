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
 *     @OA\Property(property="price", type="number", format="decimal", example=75000, description="Current price of the product"),
 *     @OA\Property(property="old_price", type="number", format="decimal", example=70000, description="Price when item was added to cart"),
 *     @OA\Property(property="total_price", type="number", format="decimal", example=150000, description="Total price (quantity × current price)"),
 *     @OA\Property(property="product_name", type="string", example="محصول نمونه", description="Product name"),
 *     @OA\Property(property="product_image", type="string", example="https://example.com/image.jpg", description="Product image URL"),
 *     @OA\Property(property="product_slug", type="string", example="sample-product", description="Product slug for URL")
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
            'price' => config('general.show_price')($this->price),
            'total_price' => config('general.show_price')($this->total_price),
            'product_name' => $this->product->name,
            'product_image' => asset('storage/' . $this->product->images->first()->image_url),
            'product_slug' => $this->product->slug,
        ];
    }
} 