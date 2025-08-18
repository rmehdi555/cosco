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
 *   @OA\Property(property="brand_name", type="string", example="سامسونگ"),
 *   @OA\Property(property="brand_slug", type="string", example="samsung"),
 *   @OA\Property(property="brand_image_url", type="string", example="https://example.com/brands/samsung.png"),
 *   @OA\Property(property="category_name", type="string", example="موبایل"),
 *   @OA\Property(property="category_slug", type="string", example="mobile"),
 *   @OA\Property(property="category_image_url", type="string", example="https://example.com/categories/mobile.png"),
 *   @OA\Property(property="name", type="string", example="گوشی موبایل سامسونگ"),
 *   @OA\Property(property="slug", type="string", example="samsung-mobile"),
 *   @OA\Property(property="description", type="string", example="توضیحات محصول"),
 *   @OA\Property(property="body", type="string", example="توضیحات کامل محصول"),
 *   @OA\Property(property="price", type="integer", example=12990000),
 *   @OA\Property(property="stock", type="integer", example=10),
 *   @OA\Property(property="count", type="integer", example=10),
 *   @OA\Property(property="count_for_user", type="integer", example=3),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 *   @OA\Property(property="is_featured", type="boolean", example=false),
 *   @OA\Property(property="is_online_only", type="boolean", example=false),
 *   @OA\Property(
 *     property="image_url",
 *     type="array",
 *     @OA\Items(type="string", format="url", example="http://localhost:8000/storage/products/image1.jpg")
 *   ),
 *   @OA\Property(
 *     property="type_buy",
 *     type="array",
 *     @OA\Items(
 *       type="object",
 *       @OA\Property(property="text", type="string", example="خرید انلاین"),
 *       @OA\Property(property="bg_color", type="string", example="#005dab")
 *     )
 *   ),
 *   @OA\Property(
 *     property="similar_products",
 *     type="array",
 *     @OA\Items(
 *       type="object",
 *       @OA\Property(property="id", type="integer", example=1),
 *       @OA\Property(property="name", type="string", example="محصول اول"),
 *       @OA\Property(property="slug", type="string", example="mhsol-aol"),
 *       @OA\Property(property="description", type="string", example="توضیحات محصول اول"),
 *       @OA\Property(property="price", type="integer", example=10000),
 *       @OA\Property(
 *         property="image_url",
 *         type="array",
 *         @OA\Items(type="string", format="url", example="https://api.rdst.ca/storage/product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg")
 *       ),
 *       @OA\Property(property="rate", type="integer", example=2),
 *       @OA\Property(property="number_rate", type="integer", example=741),
 *       @OA\Property(property="discount_price", type="integer", example=0),
 *       @OA\Property(
 *         property="type_buy",
 *         type="array",
 *         @OA\Items(
 *           type="object",
 *           @OA\Property(property="text", type="string", example="خرید حضوری"),
 *           @OA\Property(property="bg_color", type="string", example="#008000")
 *         )
 *       )
 *     )
 *   ),
 *   @OA\Property(
 *     property="recent_products",
 *     type="array",
 *     description="محصولات اخیراً مشاهده شده توسط کاربر (فقط در خروجی متد show کنترلر محصول، از دیتابیس)",
 *     @OA\Items(ref="#/components/schemas/ProductResource")
 *   ),
 *   @OA\Property(property="rate", type="integer", example=2),
 *   @OA\Property(property="number_rate", type="integer", example=741),
 *   @OA\Property(
 *     property="breadcrumb",
 *     type="array",
 *     @OA\Items(
 *       type="object",
 *       @OA\Property(property="name", type="string", example="کالای دیجیتال"),
 *       @OA\Property(property="slug", type="string", example="digital-goods")
 *     )
 *   ),
 *   @OA\Property(property="brand", ref="#/components/schemas/BrandResource"),
 *   @OA\Property(property="category", ref="#/components/schemas/ProductCategoryResource"),
 *   @OA\Property(property="reviews", type="array", @OA\Items(ref="#/components/schemas/ProductReviewResource"))
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
            'product_category_id' => $this->product_category_id,
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand->name,
            'brand_slug' => $this->brand->slug,
            'brand_image_url' => $this->brand->image_url ? asset('storage/' . $this->brand->image_url) : null,
            'category_name' => $this->category->name,
            'category_slug' => $this->category->slug,
            'category_image_url' => $this->category->image_url ? asset('storage/' . $this->category->image_url) : null,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'body' => $this->body ?? '',
            'price' => config('general.show_price')($this->price),
            'count' => $this->stock,
            'count_for_user' => $this->stock,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_online_only' => $this->is_online_only,
            'image_url' => $images,
            'type_buy' => $type_buy,
            'rate' => $average_rate,
            'number_rate' => $count_rate,
            'discount_price' => 0,
            'breadcrumb' => $this->category->getBreadcrumb(),
            'reviews' => ProductReviewResource::collection($this->whenLoaded('reviews')),
            //            'images' => $this->whenLoaded('images', function () {
//                return $this->images->map(function ($img) {
//                    return $img->image_url ? asset('storage/' . $img->image_url) : null;
//                })->filter()->values();
//            }, []),
        ];
    }
}
