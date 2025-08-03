<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/articles",
     *   summary="Get all articles (with optional search by title or category)",
     *   tags={"Article"},
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     description="Search in article title or category name/title/slug",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of articles",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ArticleResource"))
     *   )
     * )
     */
    public function index()
    {
        $query = Article::query();
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhereHas('category', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%")
                         ->orWhere('title', 'like', "%$search%")
                         ->orWhere('slug', 'like', "%$search%")
                         ;
                  });
            });
        }
        $articles = $query->get();
        return ArticleResource::collection($articles);
    }

    /**
     * @OA\Get(
     *   path="/api/articles/{slug}",
     *   summary="Get a single article",
     *   tags={"Article"},
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     description="Article slug",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Article details",
     *     @OA\JsonContent(ref="#/components/schemas/ArticleResource")
     *   )
     * )
     */
    public function show($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        return new ArticleResource($article);
    }
} 