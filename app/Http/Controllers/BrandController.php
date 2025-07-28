<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Resources\BrandResource;

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
} 