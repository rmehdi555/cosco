<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/products/{slug}",
     *   summary="Get a single product with category, brand, and reviews",
     *   tags={"Product"},
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     description="Product slug",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Product details",
     *     @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *   )
     * )
     */
    public function show($slug)
    {
        try {
            $product = Product::with(['category', 'brand', 'reviews' => function($q) { $q->where('approved', true); }])->where('slug', $slug)->firstOrFail();
            return ApiResponse::success(new ProductResource($product));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('errors.product_not_found'));
        }
    }
} 