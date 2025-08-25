<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentIndexRequest;
use App\Services\OnlinePaymentService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use App\Http\Responses\ApiResponse;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;

/**
 * @OA\Tag(
 *     name="Payment",
 *     description="API endpoints for payment operations"
 * )
 */
class PaymentController extends Controller
{
    protected OnlinePaymentService $paymentService;

    public function __construct(OnlinePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    /**
     * @OA\Get(
     *     path="/api/payments",
     *     operationId="getUserPayments",
     *     tags={"Payments"},
     *     summary="Get user payments list",
     *     description="Returns a paginated list of payments for the authenticated user with available filter options.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by payment status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "completed", "failed"})
     *     ),
     *     @OA\Parameter(
     *         name="method",
     *         in="query",
     *         description="Filter by payment method",
     *         required=false,
     *         @OA\Schema(type="string", enum={"online", "cash", "bank_transfer"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="لیست پرداخت‌ها با موفقیت دریافت شد."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="payments",
     *                     type="object",
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(ref="#/components/schemas/PaymentResource")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=5),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="total", type="integer", example=75),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="to", type="integer", example=15),
     *                     @OA\Property(property="has_more_pages", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="first", type="string", example="https://api.example.com/payments?page=1"),
     *                         @OA\Property(property="last", type="string", example="https://api.example.com/payments?page=5"),
     *                         @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                         @OA\Property(property="next", type="string", example="https://api.example.com/payments?page=2")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="filters",
     *                     type="object",
     *                     @OA\Property(
     *                         property="status",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="value", type="string", example="pending"),
     *                             @OA\Property(property="label", type="string", example="در انتظار")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="method",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="value", type="string", example="online"),
     *                             @OA\Property(property="label", type="string", example="آنلاین")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(PaymentIndexRequest $request): JsonResponse
    {
        $user = $request->user();

        $query = Payment::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->when(
                isset($request->status),
                fn($q) => $q->where('status', $request->status)
            )->when(
                isset($request->method),
                fn($q) => $q->where('method', $request->method))
            ->with(['order.shippingAddress.country', 'order.shippingAddress.province', 'order.shippingAddress.city'])
            ->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 15);
        $payments = $query->paginate($perPage);

        // Get available filter options from enums
        $statusOptions = collect(PaymentStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        })->toArray();

        $methodOptions = collect(PaymentMethod::cases())->map(function ($method) {
            return [
                'value' => $method->value,
                'label' => $method->label(),
            ];
        })->toArray();

        return ApiResponse::success([
            'payments' => PaymentResource::collection($payments),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
                'has_more_pages' => $payments->hasMorePages(),
                'links' => [
                    'first' => $payments->url(1),
                    'last' => $payments->url($payments->lastPage()),
                    'prev' => $payments->previousPageUrl(),
                    'next' => $payments->nextPageUrl(),
                ]
            ],
            'filters' => [
                'status' => $statusOptions,
                'method' => $methodOptions,
            ]
        ], trans('payments.list_retrieved_success'));
    }

    /**
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     operationId="getPayment",
     *     tags={"Payments"},
     *     summary="Get payment details",
     *     description="Returns details of a specific payment (only user's own payments)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Payment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="جزئیات پرداخت با موفقیت دریافت شد."),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/PaymentResource"
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
     *         description="Forbidden - Not authorized to view this payment",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Request $request, Payment $payment): JsonResponse
    {
        $user = $request->user();

        // Check if the payment belongs to the authenticated user
        if ($payment->order->user_id !== $user->id) {
            return ApiResponse::error(trans('payments.not_authorized_to_view'), null, 403);
        }

        $payment->load(['order.shippingAddress.country', 'order.shippingAddress.province', 'order.shippingAddress.city']);

        return ApiResponse::success(new PaymentResource($payment), trans('payments.details_retrieved_success'));
    }

    /**
     * @OA\Post(
     *     path="/api/payment/send-to-gateway",
     *     summary="Send payment request to gateway",
     *     description="This API sends a payment request to the payment gateway. On success, it returns the gateway URL and transaction ID.",
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id"},
     *             @OA\Property(property="order_id", type="integer", example=1, description="Order ID to pay for"),
     *             @OA\Property(property="gateway", type="string", example="melli_test", description="Payment gateway (optional)"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment request sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="درخواست پرداخت با موفقیت ارسال شد."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="payment_id", type="integer", example=1),
     *                 @OA\Property(property="gateway_url", type="string", example="https://sandbox.zarinpal.com/pg/StartPay/A000000000000000000000000000000000000"),
     *                 @OA\Property(property="transaction_id", type="string", example="A000000000000000000000000000000000000"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error processing payment request"),
     *         )
     *     )
     * )
     */
    public function sendToGateway(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'gateway' => 'nullable|string'
        ]);

        $order = Order::with('user')->find($request->order_id);

        if (!$order) {
            return ApiResponse::error(trans('orders.not_found'), null, 404);
        }

        // بررسی وضعیت پرداخت
        if ($order->payment_status === 'paid') {
            return ApiResponse::error(trans('payments.order_already_paid'), null, 400);
        }

        $result = $this->paymentService->sendToGateway($order, $request->gateway);

        if ($result['success']) {
            return ApiResponse::success($result, trans('payments.gateway_request_sent_success'));
        } else {
            return ApiResponse::error($result['message'] ?? trans('payments.gateway_request_failed'), null, 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment/callback",
     *     summary="Payment callback from gateway",
     *     description="This endpoint receives the response from the payment gateway. After completing the operation, the gateway redirects the user to this address.",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="gateway",
     *         in="query",
     *         description="Payment gateway name",
     *         required=false,
     *         @OA\Schema(type="string", example="melli_test")
     *     ),
     *     @OA\Parameter(
     *         name="Token",
     *         in="query",
     *         description="Transaction token from Melli bank gateway",
     *         required=false,
     *         @OA\Schema(type="string", example="123456789")
     *     ),
     *     @OA\Parameter(
     *         name="ResCod",
     *         in="query",
     *         description="Response code from gateway (0 for success)",
     *         required=false,
     *         @OA\Schema(type="string", example="0")
     *     ),
     *     @OA\Parameter(
     *         name="RefNum",
     *         in="query",
     *         description="Payment reference number",
     *         required=false,
     *         @OA\Schema(type="string", example="123456789")
     *     ),
     *     @OA\Parameter(
     *         name="authority",
     *         in="query",
     *         description="Transaction ID from Zarinpal gateway",
     *         required=false,
     *         @OA\Schema(type="string", example="A000000000000000000000000000000000000")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Payment status (OK for success, Cancel for failure) - Zarinpal only",
     *         required=false,
     *         @OA\Schema(type="string", example="OK", enum={"OK", "Cancel"})
     *     ),
     *     @OA\Parameter(
     *         name="ref_id",
     *         in="query",
     *         description="Payment reference ID - Zarinpal only",
     *         required=false,
     *         @OA\Schema(type="string", example="123456789")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment result page",
     *         @OA\MediaType(
     *             mediaType="text/html",
     *             @OA\Schema(type="string", description="HTML page displaying payment result")
     *         )
     *     )
     * )
     */
    public function callback(Request $request): View
    {
        $callbackData = $request->all();
        $gateway = $request->get('gateway');

        $result = $this->paymentService->verifyPayment($callbackData, $gateway);

        return view('payment.result', [
            'success' => $result['success'],
            'message' => $result['message'],
            'order' => $result['order'] ?? null,
            'payment' => $result['payment'] ?? null,
            'gateway_response' => $result['gateway_response'] ?? null,
            'callback_data' => $callbackData
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/payment/status/{order_id}",
     *     summary="Get payment status for an order",
     *     description="This API checks the payment status of a specific order. It returns complete order, user and latest payment information.",
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment status retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="وضعیت پرداخت با موفقیت دریافت شد."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="order_id", type="integer", example=1, description="Order ID"),
     *                 @OA\Property(property="payment_status", type="string", example="paid", description="Payment status"),
     *                 @OA\Property(property="order_status", type="string", example="processing", description="Order status"),
     *                 @OA\Property(property="total_amount", type="number", example=100000, description="Total order amount"),
     *                 @OA\Property(
     *                     property="user_info",
     *                     type="object",
     *                     description="User information",
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="cell_phone", type="string", example="09123456789"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                 ),
     *                 @OA\Property(
     *                     property="payment_info",
     *                     type="object",
     *                     description="Latest payment information",
     *                     @OA\Property(property="payment_id", type="integer", example=1),
     *                     @OA\Property(property="method", type="string", example="Online"),
     *                     @OA\Property(property="status", type="string", example="Completed"),
     *                     @OA\Property(property="amount", type="number", example=100000),
     *                     @OA\Property(property="transaction_id", type="string", example="A000000000000000000000000000000000000"),
     *                     @OA\Property(property="reference_id", type="string", example="123456789"),
     *                     @OA\Property(property="paid_at", type="string", example="2024-01-01T12:00:00.000000Z"),
     *                     @OA\Property(property="created_at", type="string", example="2024-01-01T11:00:00.000000Z"),
     *                 ),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01T10:00:00.000000Z", description="Order creation date"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-01-01T12:00:00.000000Z", description="Order last update date"),
     *                 @OA\Property(
     *                     property="status_summary",
     *                     type="object",
     *                     description="Status summary",
     *                     @OA\Property(property="is_paid", type="boolean", example=true),
     *                     @OA\Property(property="is_pending", type="boolean", example=false),
     *                     @OA\Property(property="is_failed", type="boolean", example=false),
     *                     @OA\Property(property="can_retry_payment", type="boolean", example=false),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving information"),
     *         )
     *     )
     * )
     */
    public function getPaymentStatus(int $orderId): JsonResponse
    {
        try {
            // دریافت سفارش با اطلاعات مرتبط
            $order = Order::with(['user', 'payments' => function ($query) {
                $query->latest()->first();
            }])->find($orderId);

            if (!$order) {
                return ApiResponse::error(trans('orders.not_found'), null, 404);
            }

            // دریافت آخرین پرداخت
            $latestPayment = $order->payments->first();

            // آماده‌سازی اطلاعات کاربر
            $userInfo = null;
            if ($order->user) {
                $userInfo = [
                    'user_id' => $order->user->id,
                    'name' => $order->user->name,
                    'cell_phone' => $order->user->cell_phone,
                    'email' => $order->user->email,
                ];
            }

            // آماده‌سازی اطلاعات پرداخت
            $paymentInfo = null;
            if ($latestPayment) {
                $paymentInfo = [
                    'payment_id' => $latestPayment->id,
                    'method' => $latestPayment->method?->label(),
                    'status' => $latestPayment->status?->label(),
                    'amount' => $latestPayment->amount,
                    'transaction_id' => $latestPayment->bank_transaction_id,
                    'reference_id' => $latestPayment->bank_reference_id,
                    'paid_at' => $latestPayment->paid_at,
                    'created_at' => $latestPayment->created_at,
                ];
            }

            return ApiResponse::success([
                'order_id' => $order->id,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
                'total_amount' => $order->total_amount,
                'user_info' => $userInfo,
                'payment_info' => $paymentInfo,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'status_summary' => [
                    'is_paid' => $order->payment_status === 'paid',
                    'is_pending' => $order->payment_status === 'pending',
                    'is_failed' => $order->payment_status === 'failed',
                    'can_retry_payment' => $order->payment_status === 'failed' || $order->payment_status === 'pending',
                ]
            ], trans('payments.status_retrieved_success'));

        } catch (\Exception $e) {
            Log::error('Payment Status Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::error(trans('payments.error_retrieving_information'), null, 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/payment/gateways",
     *     summary="Get active payment gateways",
     *     description="This API returns a list of all active payment gateways and their configurations.",
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Active gateways retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="درگاه‌های پرداخت با موفقیت دریافت شدند."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="gateways",
     *                     type="object",
     *                     description="Active payment gateways",
     *                     @OA\Property(
     *                         property="melli_test",
     *                         type="object",
     *                         description="Melli bank test gateway",
     *                         @OA\Property(property="name", type="string", example="Melli Bank Test"),
     *                         @OA\Property(property="type", type="string", example="melli_test"),
     *                         @OA\Property(property="enabled", type="boolean", example=true),
     *                         @OA\Property(property="merchant_id", type="string", example="46645"),
     *                         @OA\Property(property="terminal_id", type="string", example="GBHDTY98"),
     *                     ),
     *                     @OA\Property(
     *                         property="zarinpal_test",
     *                         type="object",
     *                         description="Zarinpal test gateway",
     *                         @OA\Property(property="name", type="string", example="Zarinpal Test"),
     *                         @OA\Property(property="type", type="string", example="zarinpal_test"),
     *                         @OA\Property(property="enabled", type="boolean", example=true),
     *                         @OA\Property(property="merchant_id", type="string", example="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"),
     *                     ),
     *                     @OA\Property(
     *                         property="zarinpal",
     *                         type="object",
     *                         description="Zarinpal main gateway",
     *                         @OA\Property(property="name", type="string", example="Zarinpal"),
     *                         @OA\Property(property="type", type="string", example="zarinpal"),
     *                         @OA\Property(property="enabled", type="boolean", example=false),
     *                         @OA\Property(property="merchant_id", type="string", example="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"),
     *                     ),
     *                 ),
     *                 @OA\Property(property="default_gateway", type="string", example="melli_test", description="Default gateway"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving gateway list"),
     *         )
     *     )
     * )
     */
    public function getGateways(): JsonResponse
    {
        $gateways = $this->paymentService->getActiveGateways();

        return ApiResponse::success($gateways, trans('payments.gateways_retrieved_success'));
    }


}
