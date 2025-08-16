<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Responses\ApiResponse;

/**
 * @OA\Tag(
 *     name="Order Items",
 *     description="API endpoints for order items"
 * )
 */
class OrderItemController extends Controller
{
    /**
     * Display the specified order item
     * 
     * @OA\Get(
     *     path="/api/order-items/{id}",
     *     operationId="getOrderItem",
     *     tags={"Order Items"},
     *     summary="Get a specific order item",
     *     description="Returns details of a specific order item (only user's own order items)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order Item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OrderItemResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to view this order item",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order item not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Request $request, OrderItem $orderItem)
    {
        $user = $request->user();
        
        // Check if the order item belongs to the authenticated user
        if ($orderItem->order->user_id !== $user->id) {
            abort(403, __('order_items.not_authorized_to_view'));
        }

        $orderItem->load(['product']);

        return ApiResponse::success(new OrderItemResource($orderItem));
    }
} 