<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="DiscountTypeResource",
 *   type="object",
 *   title="Discount Type Resource",
 *   description="Discount type resource representation",
 *   @OA\Property(
 *     property="ads",
 *     type="object",
 *     @OA\Property(property="title", type="string", example="عنوان تبلیغ"),
 *     @OA\Property(property="footer", type="string", example="فوتر تبلیغ"),
 *     @OA\Property(property="image_url", type="string", example="http://localhost:8000/storage/ads/ad.jpg"),
 *     @OA\Property(property="link", type="string", example="https://example.com"),
 *     @OA\Property(property="target", type="string", example="_blank")
 *   ),
 *   @OA\Property(
 *     property="sliders",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/SliderResource")
 *   ),
 *   @OA\Property(
 *     property="product_features",
 *     type="object",
 *     @OA\Property(property="title", type="string", example="عنوان ویژگی محصول"),
 *     @OA\Property(property="footer", type="string", example="فوتر ویژگی"),
 *     @OA\Property(property="image_url", type="string", example="http://localhost:8000/storage/features/feature.jpg"),
 *     @OA\Property(property="link", type="string", example="https://example.com"),
 *     @OA\Property(property="target", type="string", example="_blank"),
 *     @OA\Property(property="background_color_up", type="string", example="#ffffff"),
 *     @OA\Property(property="background_color_down", type="string", example="#f0f0f0"),
 *     @OA\Property(
 *       property="products",
 *       type="array",
 *       @OA\Items(ref="#/components/schemas/ProductSlidersResource")
 *     )
 *   )
 * )
 */
class DiscountTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ads' => [
                'title' => $this->ads_title,
                'footer' => $this->ads_footer,
                'image_url' => asset('storage/' . $this->ads_image_url),
                'link' => $this->ads_link,
                'target' => $this->ads_target,
                'background_color_up' => $this->ads_background_color_up,
                'background_color_down' => $this->ads_background_color_down,
            ],
            'sliders' => $this->productCategory && $this->productCategory->sliders->isNotEmpty()
                ? SliderResource::collection($this->productCategory->sliders)
                : [],
            'product_features' => [
                'title' => $this->title,
                'footer' => $this->footer,
                'image_url' => asset('storage/' . $this->image_url),
                'link' => $this->link,
                'target' => $this->target,
                'background_color_up' => $this->background_color_up,
                'background_color_down' => $this->background_color_down,
                'products' => $this->productCategory && $this->productCategory->products->isNotEmpty()
                    ? ProductSlidersResource::collection($this->productCategory->products)
                    : []
            ]
        ];
    }
}
