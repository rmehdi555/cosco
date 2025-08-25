<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCartRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartItemResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Enums\CartStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use App\Http\Responses\ApiResponse;

/**
 * @OA\Tag(
 *     name="Carts",
 *     description="API endpoints for user carts"
 * )
 */
class CartController extends Controller
{
    /**
     * Update user's cart with new items (replace existing items)
     *
     * @OA\Post(
     *     path="/api/carts",
     *     operationId="updateCart",
     *     tags={"Carts"},
     *     summary="Update user's cart",
     *     description="Updates the user's cart with new items, replacing all existing items. Creates a new cart if user doesn't have one.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateCartRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="سبد خرید با موفقیت بروزرسانی شد"),
     *             @OA\Property(property="data", ref="#/components/schemas/CartResource"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cart created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="سبد خرید با موفقیت ایجاد شد"),
     *             @OA\Property(property="data", ref="#/components/schemas/CartResource"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(CreateCartRequest $request): JsonResponse
    {
        $user = $request->user();
        $items = $request->validated('items') ?? [];

        try {
            DB::beginTransaction();

            // Find existing cart or create new one
            $cart = Cart::where('user_id', $user->id)
                ->where('status', CartStatus::PENDING)
                ->first();

            $isNewCart = !$cart;

            if (!$cart) {
                // Create new cart
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'status' => CartStatus::PENDING,
                    'total_amount' => 0, // Will be calculated after adding items
                ]);
            } else {
                // Delete existing cart items
                $cart->cartItems()->delete();
            }

            $totalAmount = 0;

            // Create new cart items
            foreach ($items as $item) {
                $product = Product::where('slug', $item['product_slug'])->firstOrFail();

                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'] ?? 1,
                    'description' => $item['description'],
                    'price' => $product->price,
                ]);

                $totalAmount += $cartItem->price * $cartItem->quantity;
            }

            // Update cart total amount
            $cart->update(['total_amount' => $totalAmount]);

            // Load relationships for response
            $cart->load(['cartItems.product']);

            DB::commit();

            $message = $isNewCart ? __('cart.created_successfully') : __('cart.updated_successfully');
            $statusCode = $isNewCart ? 201 : 200;

            return ApiResponse::success(
                new CartResource($cart),
                $message,
                $statusCode
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::serverError(
                $isNewCart ? __('cart.creation_failed') : __('cart.update_failed'),
                $e->getMessage()
            );
        }
    }

    /**
     * Get user's cart items
     *
     * @OA\Get(
     *     path="/api/carts",
     *     operationId="getUserCart",
     *     tags={"Carts"},
     *     summary="Get user's cart",
     *     description="Returns the authenticated user's current cart with all items",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cart retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="سبد خرید با موفقیت دریافت شد"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cart",
     *                     ref="#/components/schemas/CartResource"
     *                 ),
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/CartItemResource")
     *                 )
     *             ),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Find user's current cart
        $cart = Cart::where('user_id', $user->id)
            ->where('status', CartStatus::PENDING)
            ->with(['cartItems.product'])
            ->first();

        if (!$cart) {
            return ApiResponse::error(__('cart.not_found'), null, 404);
        }

        return ApiResponse::success([
            'cart' => new CartResource($cart),
            'items' => CartItemResource::collection($cart->cartItems)
        ], __('cart.retrieved_successfully'));
    }
}
