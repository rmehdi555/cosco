<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ProductSlidersResource",
 *   type="object",
 *   title="Product Sliders Resource",
 *   description="Product resource for sliders representation",
 *   @OA\Property(property="name", type="string", example="نام محصول"),
 *   @OA\Property(property="slug", type="string", example="product-slug"),
 *   @OA\Property(property="description", type="string", example="توضیحات کوتاه محصول"),
 *   @OA\Property(property="price", type="integer", example=150000),
 *   @OA\Property(property="rate", type="integer", example=2),
 *   @OA\Property(property="number_rate", type="integer", example=846),
 *   @OA\Property(property="discount_price", type="integer", example=3000),
 *   @OA\Property(
 *       property="type_buy",
 *       type="array",
 *       @OA\Items(
 *           type="object",
 *           @OA\Property(property="text", type="string", example="خرید آنلاین"),
 *           @OA\Property(property="bg_color", type="string", example="#005dab")
 *       )
 *   )
 * )
 */
class ProductSlidersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (int)$this->price,
            'rate' => 2,
            'number_rate' => 741,
            'discount_price' => 5000,
            'type_buy' => [[
                'text' => $this->is_online_only == true ? 'خرید انلاین' : '',
                'bg_color' => '#005dab'
            ]]
        ];
    }
}
