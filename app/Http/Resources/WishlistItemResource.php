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
 *   @OA\Property(property="wishlist_id", type="integer", example=2),
 *   @OA\Property(property="product_id", type="integer", example=3),
 *   @OA\Property(property="product", ref="#/components/schemas/ProductResource"),
 * )
 */
class WishlistItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'wishlist_id' => $this->wishlist_id,
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
} 