<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use App\Http\Resources\ArticleCategoryResource;

class ArticleCategoryController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/article-categories",
     *   summary="Get all article categories",
     *   tags={"ArticleCategory"},
     *   @OA\Response(
     *     response=200,
     *     description="List of article categories",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ArticleCategoryResource"))
     *   )
     * )
     */
    public function index()
    {
        $categories = ArticleCategory::with('articles')->get();
        return ArticleCategoryResource::collection($categories);
    }

    /**
     * @OA\Get(
     *   path="/api/article-categories/{id}",
     *   summary="Get a single article category",
     *   tags={"ArticleCategory"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Category ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Article category details",
     *     @OA\JsonContent(ref="#/components/schemas/ArticleCategoryResource")
     *   )
     * )
     */
    public function show($id)
    {
        $category = ArticleCategory::with('articles')->findOrFail($id);
        return new ArticleCategoryResource($category);
    }
} 