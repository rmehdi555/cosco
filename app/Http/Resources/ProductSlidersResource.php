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
 *   @OA\Property(property="code", type="string", example="PRD-001", description="کد یکتای محصول"),
 *   @OA\Property(property="slug", type="string", example="product-slug"),
 *   @OA\Property(property="description", type="string", example="توضیحات کوتاه محصول"),
 *   @OA\Property(property="price", type="integer", example=150000),
 *   @OA\Property(property="rate", type="integer", example=2),
 *   @OA\Property(property="number_rate", type="integer", example=846),
 *   @OA\Property(property="discount_price", type="integer", example=3000),
 *   @OA\Property(
 * *       property="image_url",
 * *       type="array",
 * *       @OA\Items(type="string", example="http://localhost:8000/storage/features/feature.jpg")
 * *   ),
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
            'code' => $this->code,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => config('general.show_price')($this->price),
            'image_url' => [$this->imagesArray()[0]],
            'rate' => $this->averageRate(),
            'number_rate' => $this->countRate(),
            'discount_price' => 0,
            'type_buy' => $this->typeBuy(),
        ];
    }
}
