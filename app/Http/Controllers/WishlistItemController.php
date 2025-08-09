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
     *     response=201,
     *     description="Wishlist item created",
     *     @OA\JsonContent(ref="#/components/schemas/WishlistItemResource")
     *   )
     * )
     */
    public function store(Request $request, $wishlist)
    {
        $user = Auth::user();
        $wishlist = Wishlist::where('user_id', $user->id)->findOrFail($wishlist);
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);
        $item = WishlistItem::firstOrCreate([
            'wishlist_id' => $wishlist->id,
            'product_id' => $data['product_id'],
        ]);
        return new WishlistItemResource($item);
    }

    /**
     * @OA\Delete(
     *   path="/api/wishlists/{wishlist}/items/{item}",
     *   summary="Remove a product from a wishlist for the authenticated user",
     *   tags={"WishlistItem"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="wishlist",
     *     in="path",
     *     required=true,
     *     description="Wishlist ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="item",
     *     in="path",
     *     required=true,
     *     description="Wishlist Item ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=204,
     *     description="Wishlist item deleted"
     *   )
     * )
     */
    public function destroy($wishlist, $item)
    {
        $user = Auth::user();
        $wishlist = Wishlist::where('user_id', $user->id)->findOrFail($wishlist);
        $wishlistItem = WishlistItem::where('wishlist_id', $wishlist->id)->findOrFail($item);
        $wishlistItem->delete();
        return ApiResponse::success(null, __('messages.deleted_successfully'), 204);
    }
} 