<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Http\Resources\WishlistItemResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\ApiResponse;

class WishlistItemController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/wishlists/{wishlist}/items",
     *   summary="Add a product to a wishlist for the authenticated user",
     *   tags={"WishlistItem"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="wishlist",
     *     in="path",
     *     required=true,
     *     description="Wishlist ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"product_id"},
     *       @OA\Property(property="product_id", type="integer", example=3)
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Wishlist item created successfully",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=200),
     *       @OA\Property(property="message", type="string", example="محصول به لیست علاقه‌مندی‌ها اضافه شد"),
     *       @OA\Property(property="data", ref="#/components/schemas/WishlistItemResource"),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Wishlist not found",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=404),
     *       @OA\Property(property="message", type="string", example="لیست علاقه‌مندی مورد نظر یافت نشد"),
     *       @OA\Property(property="data", type="null", example=null),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   )
     * )
     */
    public function store(Request $request, $wishlist)
    {
        $user = Auth::user();
        
        // Check if wishlist exists and belongs to user
        $wishlist = Wishlist::where('user_id', $user->id)->find($wishlist);
        if (!$wishlist) {
            return ApiResponse::error(__('messages.wishlist_not_found'), 404);
        }
        
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);
        
        $item = WishlistItem::firstOrCreate([
            'wishlist_id' => $wishlist->id,
            'product_id' => $data['product_id'],
        ]);
        
        return ApiResponse::success(new WishlistItemResource($item), __('messages.wishlist_item_added'));
    }

    /**
     * @OA\Delete(
     *   path="/api/wishlist-items/{item}",
     *   summary="Remove a product from a wishlist for the authenticated user",
     *   tags={"WishlistItem"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="item",
     *     in="path",
     *     required=true,
     *     description="Wishlist Item ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Wishlist item deleted successfully",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=200),
     *       @OA\Property(property="message", type="string", example="محصول از لیست علاقه‌مندی‌ها حذف شد"),
     *       @OA\Property(property="data", type="null", example=null),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Access denied - item doesn't belong to user",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=403),
     *       @OA\Property(property="message", type="string", example="شما مجاز به حذف این آیتم نیستید"),
     *       @OA\Property(property="data", type="null", example=null),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Wishlist item not found",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="integer", example=404),
     *       @OA\Property(property="message", type="string", example="آیتم مورد نظر در لیست علاقه‌مندی‌ها یافت نشد"),
     *       @OA\Property(property="data", type="null", example=null),
     *       @OA\Property(property="errors", type="null", example=null)
     *     )
     *   )
     * )
     */
    public function destroy($item)
    {
        $user = Auth::user();
        
        // First check if the item exists at all
        $wishlistItem = WishlistItem::find($item);
        if (!$wishlistItem) {
            return ApiResponse::error(__('messages.wishlist_item_not_found'), 404);
        }
        
        // Then check if it belongs to the user
        $userWishlistItem = WishlistItem::with('wishlist')
            ->whereHas('wishlist', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('id', $item)
            ->first();
            
        if (!$userWishlistItem) {
            return ApiResponse::error(__('messages.wishlist_item_access_denied'), 403);
        }
        
        $userWishlistItem->delete();
        return ApiResponse::success(null, __('messages.wishlist_item_removed'));
    }
} 