<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/products/{id}",
     *   summary="Get a single product with category, brand, and reviews",
     *   tags={"Product"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Product ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Product details",
     *     @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *   )
     * )
     */
    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'reviews' => function($q) { $q->where('approved', true); }])->findOrFail($id);
        return new ProductResource($product);
    }
} 