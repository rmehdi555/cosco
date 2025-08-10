<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountTypeResource;
use App\Http\Resources\SliderResource;
use App\Http\Responses\ApiResponse;
use App\Models\DiscountType;
use App\Models\Slider;

class HomeController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/home-page/",
     *   summary="Get home page data including sliders, first picture and discount products",
     *   tags={"Home"},
     *   @OA\Response(
     *     response=200,
     *     description="Home page data",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="عملیات با موفقیت انجام شد"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="picture", ref="#/components/schemas/SliderResource"),
     *         @OA\Property(
     *           property="offer_links",
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/SliderResource")
     *         ),
     *         @OA\Property(
     *           property="discount_products",
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/DiscountTypeResource")
     *         )
     *       ),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   )
     * )
     */
    public function index()
    {
        $sliders = Slider::where('is_show', true)
            ->select('id', 'title', 'link', 'image_url', 'target', 'type')
            ->get()
            ->groupBy('type');

        $productSlider = $sliders->get('first_slider', collect());
        $firstPicture = $sliders->get('first_picture', collect())->first();

        $discountsWithProducts = DiscountType::with([
            'category.products:id,product_category_id,name,slug,description,body,price',
            'category.sliders:id,type,title,link,image_url,target'
        ])->get();

        return ApiResponse::success([
            'picture' => new SliderResource($firstPicture),
            'offer_links' => SliderResource::collection($productSlider),
            'discount_products' => DiscountTypeResource::collection($discountsWithProducts)
        ]);
    }
}
