<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCommentRequest;
use App\Http\Resources\ProductSlidersResource;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Models\ProductReview;
use App\Models\ProductReviewFile;
use App\Services\ProductViewService;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/products/{slug}",
     *   summary="Get a single product with category, brand, and reviews",
     *   tags={"Product"},
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     description="Product slug",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Product details with recent products",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=200),
     *       @OA\Property(property="message", type="string", example="عملیات با موفقیت انجام شد"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="product", ref="#/components/schemas/ProductResource"),
     *         @OA\Property(
     *           property="recent_products",
     *           type="array",
     *           description="Recent products viewed by the user (database-based)",
     *           @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *       ),
     *       @OA\Property(property="errors", type="object", nullable=true, example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Product not found",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function show($slug, Request $request, ProductViewService $viewService)
    {
        try {
            $product = Product::with(['category', 'brand', 'reviews' => function ($q) {
                $q->where('approved', true);
            }])->where('slug', $slug)->firstOrFail();

            // Track the product view
            $result = $viewService->trackView($product, $request);

            // Get recent products
            if ($result['status']) {
                $recentProducts = $viewService->getRecentProducts(20, $product, $result['user_id']);
                $recentProductsCollection = ProductSlidersResource::collection($recentProducts);
            } else
                $recentProductsCollection = [];

            $similarProducts = $viewService->getRelatedProducts($product, 20);

            $response = [
                'product' => new ProductResource($product),
                'recent_products' => $recentProductsCollection,
                'similar_products' => ProductSlidersResource::collection($similarProducts)
            ];

            return ApiResponse::success($response);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('errors.product_not_found'));
        }
    }

///        $recentSlugs = request()->cookie('browser_id');
//
//        return response()->json($recentSlugs);
    /**
     * @OA\Post(
     *   path="/api/product-comment",
     *   summary="Add a comment/review to a product",
     *   description="Add a product review with rating, description, and optional image files",
     *   tags={"Product"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         required={"rate", "product_slug"},
     *         @OA\Property(
     *           property="rate",
     *           type="integer",
     *           minimum=1,
     *           maximum=5,
     *           description="Rating from 1 to 5",
     *           example=4
     *         ),
     *         @OA\Property(
     *           property="description",
     *           type="string",
     *           nullable=true,
     *           description="Review description/comment text",
     *           example="This product is excellent quality and I highly recommend it!"
     *         ),
     *         @OA\Property(
     *           property="product_slug",
     *           type="string",
     *           description="Product slug identifier",
     *           example="iphone-14-pro-max"
     *         ),
     *         @OA\Property(
     *           property="comment[0][file]",
     *           type="string",
     *           format="binary",
     *           nullable=true,
     *           description="First image file (jpeg, png, jpg, max 5MB) - optional"
     *         ),
     *         @OA\Property(
     *           property="comment[1][file]",
     *           type="string",
     *           format="binary",
     *           nullable=true,
     *           description="Second image file (jpeg, png, jpg, max 5MB) - optional"
     *         ),
     *         @OA\Property(
     *           property="comment[2][file]",
     *           type="string",
     *           format="binary",
     *           nullable=true,
     *           description="Third image file (jpeg, png, jpg, max 5MB) - optional"
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Comment saved successfully",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=200),
     *       @OA\Property(property="message", type="string", example="نظر شما با موفقیت ثبت شد"),
     *       @OA\Property(property="data", type="boolean", example=true),
     *       @OA\Property(property="errors", type="object", nullable=true, example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized - User not authenticated",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Product not found",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=422),
     *       @OA\Property(property="message", type="string", example="Validation failed"),
     *       @OA\Property(property="data", type="object", nullable=true, example=null),
     *       @OA\Property(
     *         property="errors",
     *         type="object",
     *         example={
     *           "rate": {"The rate field is required."},
     *           "product_slug": {"The product slug field is required."},
     *           "comment": {"The comment field is required."},
     *           "comment.0.file": {"The comment.0.file must be a file."}
     *         }
     *       )
     *     )
     *   )
     * )
     */
    public function comment(ProductCommentRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = Product::whereSlug($request->product_slug)->firstOrFail();
            $productReview = ProductReview::create([
                'product_id' => $product->id,
                'description' => $request->description,
                'user_id' => Auth::id(),
                'rating' => $request->rate,
            ]);

            $now = now();
            if ($request->has('comment')) {
                $files = [];
                foreach ($request->comment as $comment) {
                    $path = $comment['file']->store('product-comments', 'public');
                    $files[] = [
                        'product_review_id' => $productReview->id,
                        'image_url' => $path,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                ProductReviewFile::insert($files);
            }

            return ApiResponse::success(true, __('messages.comment_saved'));
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::serverError(__('messages.error_comment'), $e->getMessage());
        }
    }
}
