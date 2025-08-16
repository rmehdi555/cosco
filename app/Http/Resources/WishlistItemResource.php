<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="WishlistItemResource",
 *   type="object",
 *   title="Wishlist Item Resource",
 *   description="Wishlist item resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="product_id", type="integer", example=3),
 *   @OA\Property(property="product_slug", type="string", example="product-slug"),
 *   @OA\Property(property="product_name", type="string", example="Product Name"),
 *   @OA\Property(property="product_image_url", type="string", example="https://example.com/product.jpg"),
 *   @OA\Property(property="product_price", type="string", example="100,000 تومان"),
 * )
 */
class WishlistItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_slug' => $this->product->slug,
            'product_name' => $this->product->name,
            'product_image_url' => $this->product->image_url ? asset('storage/' . $this->product->image_url) : null,
            'product_price' => config('general.show_price')($this->product->price),
        ];
    }
} 