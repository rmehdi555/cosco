<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Get(
 *     path="/api/search",
 *     summary="Global search across products, categories, and brands",
 *     description="Search for products, categories, and brands by name or slug. Returns results grouped by type.",
 *     tags={"Search"},
 *     @OA\Parameter(
 *         name="q",
 *         in="query",
 *         required=true,
 *         description="Search query (minimum 2 characters)",
 *         @OA\Schema(
 *             type="string",
 *             minLength=2,
 *             example="iPhone"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Search results grouped by type",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="عملیات با موفقیت انجام شد"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="products",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="slug", type="string", example="iphone-15"),
 *                         @OA\Property(property="name", type="string", example="iPhone 15")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="categories",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=2),
 *                         @OA\Property(property="name", type="string", example="Electronics"),
 *                         @OA\Property(property="slug", type="string", example="electronics")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="brands",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=3),
 *                         @OA\Property(property="slug", type="string", example="apple"),
 *                         @OA\Property(property="name", type="string", example="Apple")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="errors", type="null", example=null)
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
            return ApiResponse::success([
                'products' => [],
                'categories' => [],
                'brands' => []
            ]);
        }

        $products = Product::query()
            ->where('name', 'like', "%$q%")
            ->orWhere('slug', 'like', "%$q%")
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'name' => $item->name,
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
                    'slug' => $item->slug,
                ];
            });

        $brands = Brand::query()
            ->where('name', 'like', "%$q%")
            ->orWhere('slug', 'like', "%$q%")
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'name' => $item->name,
                ];
            });

        return ApiResponse::success([
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands
        ]);
    }
} 