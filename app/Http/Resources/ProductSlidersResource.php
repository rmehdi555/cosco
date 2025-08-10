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
 *   @OA\Property(property="body", type="string", example="توضیحات کامل محصول"),
 *   @OA\Property(property="price", type="integer", example=150000)
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
            'body' => $this->body,
            'rate' => 2,
            'number_rate' => 741,
            'discountPrice' => 5000,
        ];    }
}
