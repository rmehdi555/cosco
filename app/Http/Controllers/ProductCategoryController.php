<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Resources\ProductResource;

class ProductCategoryController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/product-categories/tree",
     *   summary="Get all product categories as a tree (nested children)",
     *   tags={"ProductCategory"},
     *   @OA\Response(
     *     response=200,
     *     description="Tree of product categories",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductCategoryResource"))
     *   )
     * )
     */
    public function tree()
    {
        $categories = ProductCategory::whereNull('parent_id')->with('children')->get();
        return ProductCategoryResource::collection($categories);
    }

    /**
     * @OA\Get(
     *   path="/api/product-categories/{id}/with-products",
     *   summary="Get a category with all its subcategories and all products in this category and its subcategories",
     *   tags={"ProductCategory"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Category ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Category with subcategories and products",
     *     @OA\JsonContent(
     *       @OA\Property(property="category", ref="#/components/schemas/ProductCategoryResource"),
     *       @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *     )
     *   )
     * )
     */
    public function showWithProducts($id)
    {
        $category = ProductCategory::with('allChildren')->findOrFail($id);
        $categoryIds = $this->getAllCategoryIds($category);
        $products = Product::whereIn('product_category_id', $categoryIds)->with('images')->get();
        return response()->json([
            'category' => new ProductCategoryResource($category),
            'products' => ProductResource::collection($products),
        ]);
    }

    /**
     * Recursively get all category IDs (self + all children).
     */
    private function getAllCategoryIds(ProductCategory $category): array
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getAllCategoryIds($child));
        }
        return $ids;
    }
} 