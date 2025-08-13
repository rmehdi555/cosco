<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ProductResource",
 *   type="object",
 *   title="Product Resource",
 *   description="Product resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="product_category_id", type="integer", example=2),
 *   @OA\Property(property="brand_id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="گوشی موبایل سامسونگ"),
 *   @OA\Property(property="slug", type="string", example="samsung-mobile"),
 *   @OA\Property(property="description", type="string", example="توضیحات محصول"),
 *   @OA\Property(property="body", type="string", example="توضیحات کامل محصول"),
 *   @OA\Property(property="price", type="number", format="float", example=12990000),
 *   @OA\Property(property="stock", type="integer", example=10),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 *   @OA\Property(property="is_featured", type="boolean", example=false),
 *   @OA\Property(property="is_online_only", type="boolean", example=false),
 *   @OA\Property(property="images", type="array", @OA\Items(type="string", format="url", example="http://localhost:8000/storage/products/image1.jpg")),
 *   @OA\Property(property="brand", ref="#/components/schemas/BrandResource"),
 *   @OA\Property(property="category", ref="#/components/schemas/ProductCategoryResource"),
 *   @OA\Property(property="reviews", type="array", @OA\Items(ref="#/components/schemas/ProductReviewResource")),
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $images = [];
        foreach ($this->images as $image) {
            $images[] = asset('storage/' . $image->image_url);
        }

        return [
            'id' => $this->id,
            'product_category_id' => $this->product_category_id,
            'brand_id' => $this->brand_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'body' => $this->body ?? '',
            'price' => (int)$this->price,
            'stock' => $this->stock,
            'count' => 10,
            'count_for_user' => 3,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_online_only' => $this->is_online_only,
            'image_url' => $images,
            'type_buy' => [[
                'text' => $this->is_online_only == true ? 'خرید انلاین' : 'خرید حضوری',
                'bg_color' => $this->is_online_only == true ? '#005dab' : '#008000',
            ]],
            'similar_products' => [[
                'id' => 1,
                'name' => "محصول اول",
                'slug' => 'mhsol-aol',
                'description' => 'توضیحات محصول اول',
                'price' => 10000,
                'image_url' => [
                    "http://localhost:8072/storage/product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg"
                ],
                'rate' => 2,
                'number_rate' => 741,
                'discount_price' => 0,
                'type_buy' => [[
                    'text' => 'خرید حضوری',
                    'bg_color' => '#008000',
                ]]
            ]],
            'recent_products' => [[
                'id' => 1,
                'name' => "محصول اول",
                'slug' => 'mhsol-aol',
                'description' => 'توضیحات محصول اول',
                'price' => 10000,
                'image_url' => [
                    "http://localhost:8072/storage/product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg"
                ],
                'rate' => 2,
                'number_rate' => 741,
                'discount_price' => 0,
                'type_buy' => [[
                    'text' => 'خرید حضوری',
                    'bg_color' => '#008000',
                ]]
            ]],
            'rate' => 2,
            'number_rate' => 741,
            'breadcrumb' => $this->category->getBreadcrumb(),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'reviews' => ProductReviewResource::collection($this->whenLoaded('reviews')),
            //            'images' => $this->whenLoaded('images', function () {
//                return $this->images->map(function ($img) {
//                    return $img->image_url ? asset('storage/' . $img->image_url) : null;
//                })->filter()->values();
//            }, []),
        ];
    }
}
