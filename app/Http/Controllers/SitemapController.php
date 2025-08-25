<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Article;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Sitemap",
 *     description="Public endpoints for sitemap and robots"
 * )
 */
class SitemapController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/sitemap",
     *     operationId="getSitemap",
     *     tags={"Sitemap"},
     *     summary="Get sitemap URLs",
     *     description="Returns a list of important site URLs with metadata for crawlers",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="url", type="string", example="https://example.com/articles/example-slug"),
     *                     @OA\Property(property="lastModified", type="string", example="2024-01-01"),
     *                     @OA\Property(property="changeFrequency", type="string", example="always"),
     *                     @OA\Property(property="priority", type="number", format="float", example=0.9)
     *                 )
     *             ),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $count = 0;
        $time = Carbon::now()->format('Y-m-d');
        $data = [];
        $data[$count]['url'] = asset("");
        $data[$count]['lastModified'] = $time;
        $data[$count]['changeFrequency'] = "always";
        $data[$count]['priority'] = 1;
        $count += 1;
        $data[$count]['url'] = asset("about-us");
        $data[$count]['lastModified'] = $time;
        $data[$count]['changeFrequency'] = "always";
        $data[$count]['priority'] = 0.9;
        $count += 1;
        $data[$count]['url'] = asset("contact-us");
        $data[$count]['lastModified'] = $time;
        $data[$count]['changeFrequency'] = "always";
        $data[$count]['priority'] = 0.9;
        $count += 1;
        $data[$count]['url'] = asset("login");
        $data[$count]['lastModified'] = $time;
        $data[$count]['changeFrequency'] = "always";
        $data[$count]['priority'] = 0.9;
        $count += 1;
        $data[$count]['url'] = asset("articles");
        $data[$count]['lastModified'] = $time;
        $data[$count]['changeFrequency'] = "always";
        $data[$count]['priority'] = 0.9;

        $count += 1;
        $articles = Article::select('slug')
            ->where('is_show', '!=', false)->get();
        foreach ($articles as $article) {
            $data[$count]['url'] = asset("articles/{$article->slug}");
            $data[$count]['lastModified'] = $time;
            $data[$count]['changeFrequency'] = "always";
            $data[$count]['priority'] = 0.9;
            $count += 1;
        }

        $data[$count]['url'] = asset("product");
        $data[$count]['lastModified'] = $time;
        $data[$count]['changeFrequency'] = "always";
        $data[$count]['priority'] = 0.9;
        $count += 1;

        $products = Product::select('slug')
            ->where('is_active', '!=', false)->get();

        foreach ($products as $product) {
            $data[$count]['url'] = asset("product/{$product->slug}");
            $data[$count]['lastModified'] = $time;
            $data[$count]['changeFrequency'] = "always";
            $data[$count]['priority'] = 0.7;
            $count += 1;
        }
        return ApiResponse::success($data, '');
    }

    /**
     * @OA\Get(
     *     path="/api/robots",
     *     operationId="getRobots",
     *     tags={"Sitemap"},
     *     summary="Get robots rules",
     *     description="Returns robots.txt-like rules and sitemap location",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="rules",
     *                     type="object",
     *                     @OA\Property(property="userAgent", type="string", example="*"),
     *                     @OA\Property(property="allow", type="array", @OA\Items(type="string", example="/")),
     *                     @OA\Property(property="disallow", type="array", @OA\Items(type="string", example="/dashboard"))
     *                 ),
     *                 @OA\Property(property="sitemap", type="string", example="https://example.com/sitemap.xml")
     *             ),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function robots(): JsonResponse
    {
        $data['rules']['userAgent'] = "*";
        $data['rules']['allow'] = [
            "/",
            "/about-us",
        ];
        $data['rules']['disallow'] = [
            "/dashboard",
            "/dashboard/*",
        ];
        $data['sitemap'] = asset("sitemap.xml");

        return ApiResponse::success($data, '');
    }
}
