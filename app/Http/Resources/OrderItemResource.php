<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderItemResource",
 *     title="Order Item Resource",
 *     description="Order item resource schema",
 *     @OA\Property(property="quantity", type="integer", example=2, description="Quantity of the product in order"),
 *     @OA\Property(property="price", type="number", format="decimal", example=7500, description="Product price in Toman"),
 *     @OA\Property(property="total_price", type="number", format="decimal", example=15000, description="Total price (quantity × price) in Toman"),
 *     @OA\Property(property="product_name", type="string", example="محصول نمونه", description="Product name"),
 *     @OA\Property(property="product_image", type="string", example="https://example.com/storage/products/image.jpg", description="Product image URL"),
 *     @OA\Property(property="product_slug", type="string", example="mhsol-aol", description="Product slug for URL")
 * )
 */
class OrderItemResource extends JsonResource
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