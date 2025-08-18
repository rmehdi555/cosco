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
 *   @OA\Property(property="rate", type="number", format="float", example=4.2),
 *   @OA\Property(property="number_rate", type="integer", example=15),
 *   @OA\Property(property="discount_price", type="integer", example=0),
 *   @OA\Property(property="type_buy", type="string", example="نقدی"),
 * )
 */
class WishlistItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_slug' => $this->product?->slug,
            'product_name' => $this->product?->name,
            'product_image_url' => $this->product?->mainImage?->image_url ? asset('storage/' . $this->product->mainImage->image_url) : '',
            'product_price' => $this->product?->price ? config('general.show_price')($this->product->price) : '',
            'rate' => $this->product?->averageRate() ?? 0,
            'number_rate' => $this->product?->countRate() ?? 0,
            'discount_price' => 0,
            'type_buy' => $this->product?->typeBuy() ?? '',
        ];
    }
}
