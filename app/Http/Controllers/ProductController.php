<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCommentRequest;
use App\Http\Requests\ProductCountRequest;
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
     *   summary="دریافت اطلاعات یک محصول به همراه دسته‌بندی، برند و نظرات",
     *   tags={"Product"},
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     description="اسلاگ محصول",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="جزئیات محصول به همراه محصولات اخیر و مشابه",
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
     *           description="محصولات اخیر مشاهده شده توسط کاربر (بر اساس دیتابیس)",
     *           @OA\Items(ref="#/components/schemas/ProductResource")
     *         ),
     *         @OA\Property(
     *           property="similar_products",
     *           type="array",
     *           description="محصولات مشابه پیشنهادی",
     *           @OA\Items(ref="#/components/schemas/ProductSlidersResource")
     *         )
     *       ),
     *       @OA\Property(property="errors", type="object", nullable=true, example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="محصول پیدا نشد",
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
//        return response()->json($request->validated());
        try {
            DB::beginTransaction();
            $product = Product::whereSlug($request->product_slug)->firstOrFail();
            $productReview = ProductReview::create([
                'product_id' => $product->id,
                'comment' => $request->description,
                'user_id' => Auth::id(),
                'rating' => $request->rate,
            ]);

            $now = now();
            if ($request->has('comment')) {
                $files = [];
                foreach ($request->comment as $comment) {
                    if (isset($comment['file']) and $comment['file']) {
                        $path = $comment['file']->store('product-comments', 'public');
                        $files[] = [
                            'product_review_id' => $productReview->id,
                            'image_url' => $path,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
                if (!empty($files)) {
                    ProductReviewFile::insert($files);
                }
            }

            DB::commit();
            return ApiResponse::success(true, __('messages.comment_saved'));
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::serverError(__('messages.error_comment'), $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/api/product-count",
     *   summary="Get product count and pricing information for cart items",
     *   description="Returns available stock count and current price for a list of products by their slugs",
     *   tags={"Product"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       required={"cart"},
     *       @OA\Property(
     *         property="cart",
     *         type="array",
     *         description="Array of cart items with product slugs",
     *         @OA\Items(
     *           type="object",
     *           required={"slug"},
     *           @OA\Property(property="slug", type="string", description="Product slug", example="iphone-14-pro")
     *         )
     *       ),
     *       example={
     *         "cart": {
     *           {"slug": "iphone-14-pro"},
     *           {"slug": "samsung-galaxy-s23"}
     *         }
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Product count and pricing information",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=200),
     *       @OA\Property(property="message", type="string", example="اطلاعات محصولات با موفقیت بروزرسانی شد"),
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="slug", type="string", example="iphone-14-pro"),
     *           @OA\Property(property="count", type="integer", example=25, description="Available stock count"),
     *           @OA\Property(property="price", type="integer", example=10000000, description="Formatted price")
     *         )
     *       ),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Product not found",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function count(ProductCountRequest $request)
    {
        $list = [];
        foreach ($request->cart as $key => $cart_item) {
            $a = [];
            $product = Product::
//            where('is_active', true)->
            where("slug", $cart_item['slug'])->firstOrFail();

            if ($product->stock > $product->stock)
                $count_for_user = $product->stock;
            else
                $count_for_user = $product->stock;

            $a['slug'] = $product['slug'];
            $a['count'] = $count_for_user;
            $a['price'] = config('general.show_price')($product['price']);
            $list[$key] = $a;
        }

        return ApiResponse::success($list, __('messages.item_refresh_success'));
    }

}
