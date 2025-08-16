<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticlesIndexRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Article;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/articles",
     *   summary="Get paginated articles (optional search by title or category)",
     *   tags={"Article"},
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     description="Search in article title or category (name/title/slug)",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     description="Results page number",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Parameter(
     *     name="count",
     *     in="query",
     *     required=false,
     *     description="Results per page",
     *     @OA\Schema(type="integer", example=12)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Articles list wrapped in ApiResponse with pagination",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=200),
     *       @OA\Property(property="message", type="string", example="عملیات با موفقیت انجام شد"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(
     *           property="articles",
     *           type="object",
     *           @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ArticleResource")),
     *           @OA\Property(property="total", type="integer", example=120),
     *           @OA\Property(property="perPage", type="integer", example=12),
     *           @OA\Property(property="currentPage", type="integer", example=1),
     *           @OA\Property(property="lastPage", type="integer", example=10)
     *         )
     *       ),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   )
     * )
     */
    public function index(ArticlesIndexRequest $request)
    {
        $query = Article::query();
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhereHas('category', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%")
                            ->orWhere('title', 'like', "%$search%")
                            ->orWhere('slug', 'like', "%$search%");
                    });
            });
        }
        $articles = $query->paginate($request->count ?? 12);
        return ApiResponse::success([
            'articles' => [
                'data' => ArticleResource::collection($articles),
                'total' => $articles->total(),
                'perPage' => $articles->perPage(),
                'currentPage' => $articles->currentPage(),
                'lastPage' => $articles->lastPage(),
            ],
        ]);
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
        return ApiResponse::success(new ArticleResource($article));
    }
}
