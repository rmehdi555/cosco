<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Resources\BrandResource;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/brands",
     *   summary="Get all active brands",
     *   tags={"Brand"},
     *   @OA\Response(
     *     response=200,
     *     description="List of active brands",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/BrandResource"))
     *   )
     * )
     */
    public function index()
    {
        $brands = Brand::where('is_active', true)->get();
        return BrandResource::collection($brands);
    }

    /**
     * @OA\Get(
     *   path="/api/brands/{slug}",
     *   summary="Get brand details with products",
     *   tags={"Brand"},
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     description="Brand slug",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Brand details with products",
     *     @OA\JsonContent(ref="#/components/schemas/BrandResource")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Brand not found"
     *   )
     * )
     */
    public function show(Request $request, $slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();
        
        // Load products relationship
        $brand->load(['products' => function ($query) {
            $query->where('is_active', true);
        }]);

        return new BrandResource($brand);
    }
} 