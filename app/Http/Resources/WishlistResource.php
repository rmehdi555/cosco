<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WishlistItemResource;

/**
 * @OA\Schema(
 *   schema="WishlistResource",
 *   type="object",
 *   title="Wishlist Resource",
 *   description="Wishlist resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="لیست علاقه‌مندی‌ها"),
 *   @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/WishlistItemResource")),
 * )
 */
class WishlistResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'items' => WishlistItemResource::collection($this->whenLoaded('items')),
        ];
    }
} 