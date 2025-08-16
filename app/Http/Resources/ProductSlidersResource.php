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
        $images = [];
        foreach ($this->images as $image) {
            $images[] = asset('storage/' . $image->image_url);
        }

        if ($this->is_online_only == true) {
            $type_buy = [[
                'text' => 'خرید انلاین',
                'bg_color' => '#005dab',
            ]];
        } else {
            $type_buy = [
                [
                    'text' => 'خرید انلاین',
                    'bg_color' => '#005dab',
                ],
                [
                    'text' => 'خرید حضوری',
                    'bg_color' => '#008000',
                ]
            ];
        }

        $all_rates = $this->reviews->sum('rating');
        $count_rate = $this->reviews->count();
        if ($count_rate == 0)
            $count_rate = 1;
        $average_rate = round($all_rates / $count_rate, 1);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => config('general.show_price')($this->price),
            'image_url' => [$images[0]],
            'rate' => $average_rate,
            'number_rate' => $count_rate,
            'discount_price' => 0,
            'type_buy' => $type_buy,
        ];
    }
}
