<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use App\Enums\OrderStatus;
use App\Enums\OrderPaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

    /**
     * Create a new order with items
     * 
     * @OA\Post(
     *     path="/api/orders",
     *     operationId="createOrder",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     description="Creates a new order with items for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateOrderRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="سفارش با موفقیت ایجاد شد"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OrderResource"
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Address does not belong to user",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        $user = $request->user();
        $items = $request->validated('items');
        $shippingAddressId = $request->validated('shipping_address_id');

        // Check if shipping address belongs to the user
        $shippingAddress = Address::where('id', $shippingAddressId)
            ->where('user_id', $user->id)
            ->first();

        if (!$shippingAddress) {
            return response()->json([
                'success' => false,
                'message' => __('orders.shipping_address_not_authorized'),
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::PENDING,
                'total_amount' => 0, // Will be calculated after adding items
                'payment_status' => OrderPaymentStatus::UNPAID,
                'shipping_address_id' => $shippingAddressId,
            ]);

            $totalAmount = 0;

            // Create order items
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $totalAmount += $orderItem->price * $orderItem->quantity;
            }

            // Update order total amount
            $order->update(['total_amount' => $totalAmount]);

            // Load relationships for response
            $order->load(['shippingAddress.country', 'shippingAddress.province', 'shippingAddress.city', 'orderItems.product']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('orders.created_successfully'),
                'data' => new OrderResource($order),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => __('orders.creation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 