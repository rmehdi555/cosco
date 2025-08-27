<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderIndexRequest;
use App\Http\Resources\OrderIndexResource;
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
use App\Http\Responses\ApiResponse;

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
     *     description="Returns a paginated list of orders for the authenticated user with available filter options",
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
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="count",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="عملیات با موفقیت انجام شد"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="orders",
     *                     type="object",
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(ref="#/components/schemas/OrderResource")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="total", type="integer", example=100),
     *                     @OA\Property(property="perPage", type="integer", example=5),
     *                     @OA\Property(property="currentPage", type="integer", example=1),
     *                     @OA\Property(property="lastPage", type="integer", example=20)
     *                 ),
     *                 @OA\Property(
     *                     property="filters",
     *                     type="object",
     *                     @OA\Property(
     *                         property="status",
     *                         type="array",
     *                         description="Order status options (name pairs)
     *                         ",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="name_en", type="string", example="pending"),
     *                             @OA\Property(property="name_fa", type="string", example="در انتظار")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="payment_status",
     *                         type="array",
     *                         description="Payment status options (value/label)",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="value", type="string", example="unpaid"),
     *                             @OA\Property(property="label", type="string", example="پرداخت نشده")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function index(OrderIndexRequest $request)
    {
        $user = $request->user();

        $orders = $user->orders()
            ->with(['shippingAddress.country', 'shippingAddress.province', 'shippingAddress.city', 'orderItems.product'])
            ->when(
                isset($request->status),
                fn($q) => $q->where('status', $request->status)
            )->when(
                isset($request->payment_status),
                fn($q) => $q->where('payment_status', $request->payment_status)
            )->orderBy('created_at', 'desc')->paginate($request->count ?? 5);

        $orderStatus = OrderStatus::getNamePairs();
        $orderPaymentStatus = OrderPaymentStatus::getNamePairs();

        return ApiResponse::success([
            'orders' => OrderResource::collection($orders),
            'pagination' => [
                'total' => $orders->total(),
                'perPage' => $orders->perPage(),
                'currentPage' => $orders->currentPage(),
                'lastPage' => $orders->lastPage(),
            ],
            'filters' => [
                'status' => $orderStatus,
                'payment_status' => $orderPaymentStatus,
            ]
        ]);
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
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="عملیات با موفقیت انجام شد"),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderResource"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Order does not belong to user",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== $request->user()->id) {
            abort(403, __('orders.not_authorized_to_view'));
        }

        $order->load(['shippingAddress.country', 'shippingAddress.province', 'shippingAddress.city', 'orderItems.product']);

        return ApiResponse::success(new OrderResource($order));
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
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="سفارش با موفقیت ایجاد شد"),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderResource"),
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
        $description = $request->validated('description');
        $received_at = config('general.show_gregorian')($request->validated('received_at'));

        // Check if shipping address belongs to the user
        $shippingAddress = Address::where('id', $shippingAddressId)
            ->where('user_id', $user->id)
            ->first();

        if (!$shippingAddress) {
            return ApiResponse::error(__('orders.shipping_address_not_authorized'), null, 403);
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
                'description' => $description,
                'received_at' => $received_at,
            ]);

            $totalAmount = 0;

            // Create order items
            foreach ($items as $item) {
                $product = Product::where('slug', $item['product_slug'])->firstOrFail();

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
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

            return ApiResponse::success(
                new OrderResource($order),
                __('orders.created_successfully'),
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::serverError(
                __('orders.creation_failed'),
                $e->getMessage()
            );
        }
    }
}
