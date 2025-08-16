<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Http\Resources\WishlistResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreWishlistRequest;
use App\Http\Requests\UpdateWishlistRequest;
use App\Http\Responses\ApiResponse;

class WishlistController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/wishlists",
     *   summary="Get all wishlists for the authenticated user",
     *   tags={"Wishlist"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="List of wishlists",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/WishlistResource"))
     *   )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $wishlists = Wishlist::where('user_id', $user->id)->with(['items.product'])->get();
        return ApiResponse::success(WishlistResource::collection($wishlists));
    }

    /**
     * @OA\Post(
     *   path="/api/wishlists",
     *   summary="Create a new wishlist for the authenticated user",
     *   tags={"Wishlist"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/StoreWishlistRequest")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Wishlist created",
     *     @OA\JsonContent(ref="#/components/schemas/WishlistResource")
     *   )
     * )
     */
    public function store(StoreWishlistRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $wishlist = Wishlist::create($data);
        return ApiResponse::success(new WishlistResource($wishlist));
    }

    /**
     * @OA\Put(
     *   path="/api/wishlists/{id}",
     *   summary="Update a wishlist for the authenticated user",
     *   tags={"Wishlist"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Wishlist ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/UpdateWishlistRequest")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Wishlist updated",
     *     @OA\JsonContent(ref="#/components/schemas/WishlistResource")
     *   )
     * )
     */
    public function update(UpdateWishlistRequest $request, $id)
    {
        $user = Auth::user();
        $wishlist = Wishlist::where('user_id', $user->id)->findOrFail($id);
        $data = $request->validated();
        $wishlist->update($data);
        return ApiResponse::success(new WishlistResource($wishlist));
    }

    /**
     * @OA\Get(
     *   path="/api/wishlists/{id}",
     *   summary="Get a single wishlist for the authenticated user (with items)",
     *   tags={"Wishlist"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Wishlist ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Wishlist details",
     *     @OA\JsonContent(ref="#/components/schemas/WishlistResource")
     *   )
     * )
     */
    public function show($id)
    {
        $user = Auth::user();
        $wishlist = Wishlist::where('user_id', $user->id)->with(['items.product'])->findOrFail($id);
        return ApiResponse::success(new WishlistResource($wishlist));
    }
} 