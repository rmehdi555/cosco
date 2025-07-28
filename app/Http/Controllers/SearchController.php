<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Get(
 *     path="/api/search",
 *     summary="Global search across products, categories, and brands",
 *     tags={"Search"},
 *     @OA\Parameter(
 *         name="q",
 *         in="query",
 *         required=true,
 *         description="Search query",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Search results",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="iPhone 15"),
 *                     @OA\Property(property="type", type="string", example="product"),
 *                     @OA\Property(property="link", type="string", example="/products/iphone-15")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = $request->input('q');
        if (!$q || !is_string($q) || Str::length($q) < 2) {
            return response()->json(['data' => []]);
        }

        $products = Product::query()
            ->where('name', 'like', "%$q%")
            ->orWhere('slug', 'like', "%$q%")
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => 'product',
                    'link' => url("/api/products/{$item->id}"),
                ];
            });

        $categories = ProductCategory::query()
            ->where('name', 'like', "%$q%")
            ->orWhere('slug', 'like', "%$q%")
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => 'category',
                    'link' => url("/api/product-categories/{$item->id}/with-products"),
                ];
            });

        $brands = Brand::query()
            ->where('name', 'like', "%$q%")
            ->orWhere('slug', 'like', "%$q%")
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => 'brand',
                    'link' => url("/api/brands/{$item->id}"),
                ];
            });

        $results = $products->concat($categories)->concat($brands)->values();

        return response()->json(['data' => $results]);
    }
} 