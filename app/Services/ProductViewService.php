<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Laravel\Passport\Token;

class ProductViewService
{

    public function getBrowserId()
    {
        $browserId = request()->cookie('browser_id');

        if (!$browserId) {
            $browserId = Str::uuid()->toString();
        }

        BrowserSession::updateOrCreate(
            ['browser_id' => $browserId],
            ['last_activity' => now()]
        );

        return $browserId;
    }

    /**
     * Track a product view
     */
    public function trackView(Product $product, Request $request)
    {
        $result = [];
        $token = $request->cookie('browser_id');
        $result['status'] = false;
        $result['user_id'] = 0;

        if ($token) {
            $user = Token::where('id', $token)->first();
            if ($result['user_id']) {
                $result['status'] = true;
                $result['user_id'] = $user->id;
            }
        }
//        dd($result['user_id']);
        if ($result['status']) {
            // Try to insert the view, ignore if duplicate
            try {
                ProductView::create([
                    'product_id' => $product->id,
                    'user_id' => $result['user_id'],
                ]);

            } catch (\Illuminate\Database\QueryException $e) {
                // Ignore duplicate entry errors
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
            }
        }
        return $result;
    }

    /**
     * Get recent products viewed by the current user/visitor
     */
    public function getRecentProducts(int $limit = 20, ?Product $excludeProduct = null, $userId = null)
    {
        $query = ProductView::with(['product.category', 'product.brand', 'product.mainImage'])
            ->whereHas('product', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('created_at', 'desc');

        $query->where('user_id', $userId);

        // Exclude current product if provided
        if ($excludeProduct) {
            $query->where('product_id', '!=', $excludeProduct->id);
        }

        $recentViews = $query->limit($limit)->get();

        // Return unique products (in case of duplicates)
        return $recentViews->unique('product_id')->pluck('product');
    }

    /**
     * Get popular products based on view count
     */
    public function getPopularProducts(int $limit = 8, ?Product $excludeProduct = null)
    {
        $query = Product::with(['category', 'brand', 'mainImage'])
            ->where('is_active', true)
            ->withCount('views')
            ->orderBy('views_count', 'desc')
            ->orderBy('created_at', 'desc');

        // Exclude current product if provided
        if ($excludeProduct) {
            $query->where('id', '!=', $excludeProduct->id);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get related products based on category and brand
     */
    public function getRelatedProducts(Product $product, int $limit = 20)
    {
        return Product::with(['category', 'brand', 'mainImage'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('product_category_id', $product->product_category_id)
                    ->orWhere('brand_id', $product->brand_id);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
