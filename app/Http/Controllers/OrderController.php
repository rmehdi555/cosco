<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="API endpoints for user orders"
 * )
 */
class OrderController extends Controller
{
    /**
     * Display user's orders
     * 
     * @OA\Get(
     *     path="/api/orders",
     *     operationId="getUserOrders",
     *     tags={"Orders"},
     *     summary="Get user's orders",
     *     description="Returns a list of all orders for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by order status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "paid", "shipped", "delivered", "cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="payment_status",
     *         in="query",
     *         description="Filter by payment status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"unpaid", "paid", "refunded"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/OrderResource")
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        
        $query = $user->orders()
            ->with(['shippingAddress.country', 'shippingAddress.province', 'shippingAddress.city', 'orderItems.product'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Display the specified order
     * 
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     operationId="getOrder",
     *     tags={"Orders"},
     *     summary="Get a specific order",
     *     description="Returns details of a specific order (only user's own orders)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
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
     *                 ref="#/components/schemas/OrderResource"
     *             )
     *         )
     *     )
     * )
     */
    public function show(Request $request, Order $order): OrderResource
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== $request->user()->id) {
            abort(403, __('orders.not_authorized_to_view'));
        }

        $order->load(['shippingAddress.country', 'shippingAddress.province', 'shippingAddress.city', 'orderItems.product']);

        return new OrderResource($order);
    }
} 