<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowWithProductRequest;
use App\Http\Resources\ProductSlidersResource;
use App\Http\Resources\ShowWithProductResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\BrandResource;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Brand;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Responses\ApiResponse;

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
        $categories = ProductCategory::whereNull('parent_id')->with('allChildren')->get();
        return ApiResponse::success(ProductCategoryResource::collection($categories));
    }

    /**
     * @OA\Get(
     *   path="/api/product-categories/{slug}/with-products",
     *   summary="دریافت دسته‌بندی به همراه زیرمجموعه‌ها و محصولات (با فیلتر و صفحه‌بندی)",
     *   tags={"ProductCategory"},
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     description="اسلاگ دسته‌بندی. مقدار 'all' برای دریافت همه دسته‌های سطح بالا و محصولات آن‌ها.",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="min_price",
     *     in="query",
     *     required=false,
     *     description="حداقل قیمت محصول",
     *     @OA\Schema(type="integer", example=1000000)
     *   ),
     *   @OA\Parameter(
     *     name="max_price",
     *     in="query",
     *     required=false,
     *     description="حداکثر قیمت محصول",
     *     @OA\Schema(type="integer", example=5000000)
     *   ),
     *   @OA\Parameter(
     *     name="sort_by",
     *     in="query",
     *     required=false,
     *     description="مرتب‌سازی محصولات بر اساس: ارزان‌ترین، گران‌ترین، جدیدترین",
     *     @OA\Schema(type="string", enum={"cheapest","expensive","newest"})
     *   ),
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     description="شماره صفحه نتایج",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Parameter(
     *     name="count",
     *     in="query",
     *     required=false,
     *     description="تعداد نتایج در هر صفحه",
     *     @OA\Schema(type="integer", example=12)
     *   ),
     *   @OA\Parameter(
     *     name="category",
     *     in="query",
     *     required=false,
     *     description="فیلتر دسته‌بندی اضافی (اختیاری)",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="brand",
     *     in="query",
     *     required=false,
     *     description="فیلتر برند بر اساس اسلاگ",
     *     @OA\Schema(type="string", example="apple")
     *   ),
     *   @OA\Parameter(
     *     name="rating",
     *     in="query",
     *     required=false,
     *     description="حداقل امتیاز محصول (1 تا 5)",
     *     @OA\Schema(type="integer", minimum=1, maximum=5, example=4)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="دسته‌بندی با زیرمجموعه‌ها، محصولات، اسلایدرها و breadcrumb در قالب ApiResponse",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=200),
     *       @OA\Property(property="message", type="string", example="عملیات با موفقیت انجام شد"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/ShowWithProductResource")),
     *         @OA\Property(
     *           property="products",
     *           type="object",
     *           @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductSlidersResource")),
     *           @OA\Property(property="total", type="integer", example=120),
     *           @OA\Property(property="perPage", type="integer", example=12),
     *           @OA\Property(property="currentPage", type="integer", example=1),
     *           @OA\Property(property="lastPage", type="integer", example=10)
     *         ),
     *         @OA\Property(property="brands", type="array", @OA\Items(ref="#/components/schemas/BrandResource")),
     *         @OA\Property(property="sliders", type="array", @OA\Items(ref="#/components/schemas/SliderResource")),
     *         @OA\Property(
     *           property="breadcrumb",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="کالای دیجیتال"),
     *             @OA\Property(property="slug", type="string", example="digital-goods")
     *           )
     *         )
     *       ),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="دسته‌بندی پیدا نشد",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function showWithProducts(ShowWithProductRequest $request)
    {
        if ($request->slug === 'all') {
            $categories = ProductCategory::
//            where('is_active', true)
            where('parent_id', null)->with('sliders')->get();

            $categoryIds = ProductCategory::pluck('id')->toArray();
            $sliders = $categories->flatMap(function ($category) {
                return $category->sliders;
            })->take(4);
            $breadcrumb = [];
        } else {
            $category = ProductCategory::with(['allChildren', 'sliders'])
//                ->where('is_active', true)
                ->where('slug', $request->slug)->firstOrFail();
            $categories = $category->allChildren;
            $categoryIds = $this->getAllCategoryIds($category);
            $sliders = $category->sliders;
            $breadcrumb = $category->getBreadcrumb();
        }

        // Get brands for these categories
        $brands = Brand::with('productCategory')
            ->whereIn('product_category_id', $categoryIds)
            ->where('is_active', true)
            ->get();

        $products = Product::whereIn('product_category_id', $categoryIds)->with('images')
            ->when(isset($request->sort_by), function ($q) use ($request) {
                if ($request->sort_by == 'cheapest') {
                    return $q->orderBy('price', 'asc');
                } elseif ($request->sort_by == 'expensive') {
                    return $q->orderBy('price', 'desc');
                } elseif ($request->sort_by == 'newest') {
                    return $q->orderBy('updated_at', 'desc');
                }
            })
            ->when(
                isset($request->min_price) and !isset($request->max_price),
                fn($q) => $q->where('price', '>=', config('general.to_rial')($request->min_price))
            )
            ->when(
                isset($request->max_price) and !isset($request->min_price),
                fn($q) => $q->where('price', '<=', config('general.to_rial')($request->max_price))
            )
            ->when(
                isset($request->max_price) and isset($request->min_price),
                fn($q) => $q->whereBetween('price', [config('general.to_rial')($request->min_price), config('general.to_rial')($request->max_price)])
            )
            ->when(
                isset($request->brand),
                fn($q) => $q->whereHas('brand', fn($brandQuery) => $brandQuery->where('slug', $request->brand))
            )
            ->when(
                isset($request->rating),
                fn($q) => $q->whereHas('reviews', fn($rating) => $rating->where('rating', '>=', $request->rating))
            )
            ->latest()->paginate($request->count ?? 12);

        return ApiResponse::success([
            'categories' => ShowWithProductResource::collection($categories),
            'products' => [
                'data' => ProductSlidersResource::collection($products),
                'total' => $products->total(),
                'perPage' => $products->perPage(),
                'currentPage' => $products->currentPage(),
                'lastPage' => $products->lastPage(),
            ],
            'brands' => BrandResource::collection($brands),
            'sliders' => SliderResource::collection($sliders),
            'breadcrumb' => $breadcrumb,
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

