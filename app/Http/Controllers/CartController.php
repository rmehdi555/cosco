<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Enums\CartStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Carts",
 *     description="API endpoints for user carts"
 * )
 */
class CartController extends Controller
{
    /**
     * Create a new cart with items
     * 
     * @OA\Post(
     *     path="/api/carts",
     *     operationId="createCart",
     *     tags={"Carts"},
     *     summary="Create a new cart",
     *     description="Creates a new cart with items for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateCartRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cart created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="سبد خرید با موفقیت ایجاد شد"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/CartResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
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
        $items = $request->validated('items');

        try {
            DB::beginTransaction();

            // Create cart
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => CartStatus::PENDING,
                'total_amount' => 0, // Will be calculated after adding items
            ]);

            $totalAmount = 0;

            // Create cart items
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $totalAmount += $cartItem->price*$cartItem->quantity;
            }

            // Update cart total amount
            $cart->update(['total_amount' => $totalAmount]);

            // Load relationships for response
            $cart->load(['cartItems.product']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('cart.created_successfully'),
                'data' => new CartResource($cart),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => __('cart.creation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 