<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MembershipTypeController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DiscountController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-email', [AuthController::class, 'verifyEmail']);
Route::post('verify-sms', [AuthController::class, 'verifySms']);
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('login-with-otp', [AuthController::class, 'loginWithOtp']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('profile/update', [AuthController::class, 'updateProfile']);
    Route::get('addresses', [\App\Http\Controllers\AddressController::class, 'index']);
    Route::post('addresses', [\App\Http\Controllers\AddressController::class, 'store']);
    Route::put('addresses/{id}', [\App\Http\Controllers\AddressController::class, 'update']);
    Route::get('wishlists', [\App\Http\Controllers\WishlistController::class, 'index']);
    Route::post('wishlists', [\App\Http\Controllers\WishlistController::class, 'store']);
    Route::put('wishlists/{id}', [\App\Http\Controllers\WishlistController::class, 'update']);
    Route::post('wishlists/{wishlist}/items', [\App\Http\Controllers\WishlistItemController::class, 'store']);
    Route::delete('wishlists/{wishlist}/items/{item}', [\App\Http\Controllers\WishlistItemController::class, 'destroy']);
    Route::get('wishlists/{id}', [\App\Http\Controllers\WishlistController::class, 'show']);

    // Membership routes
    Route::get('memberships', [MembershipController::class, 'index']);
    Route::get('memberships/status', [MembershipController::class, 'status']);
    Route::get('memberships/{membership}', [MembershipController::class, 'show']);

    // Order routes
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

    // Order Item routes
    Route::get('order-items/{orderItem}', [OrderItemController::class, 'show']);

    // Payment routes
    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('payments/{payment}', [PaymentController::class, 'show']);

    // Cart routes
    Route::post('carts', [CartController::class, 'store']);
});

// Public routes
Route::get('membership-types', [MembershipTypeController::class, 'index']);
Route::get('membership-types/{membershipType}', [MembershipTypeController::class, 'show']);

Route::get('product-categories/tree', [\App\Http\Controllers\ProductCategoryController::class, 'tree']);
Route::get('product-categories/{slug}/with-products', [\App\Http\Controllers\ProductCategoryController::class, 'showWithProducts']);
Route::get('article-categories', [\App\Http\Controllers\ArticleCategoryController::class, 'index']);
Route::get('article-categories/{slug}', [\App\Http\Controllers\ArticleCategoryController::class, 'show']);
Route::get('articles', [\App\Http\Controllers\ArticleController::class, 'index']);
Route::get('articles/{slug}', [\App\Http\Controllers\ArticleController::class, 'show']);
Route::get('countries/tree', [\App\Http\Controllers\CountryController::class, 'tree']);
Route::get('products/{slug}', [\App\Http\Controllers\ProductController::class, 'show']);
Route::get('brands', [\App\Http\Controllers\BrandController::class, 'index']);
Route::get('brands/{slug}', [\App\Http\Controllers\BrandController::class, 'show']);

Route::get('home-page/', [\App\Http\Controllers\HomeController::class, 'index']);

// Global search
Route::get('search', [\App\Http\Controllers\SearchController::class, 'search']);
Route::get('search-all', [\App\Http\Controllers\SearchController::class, 'searchAll']);
