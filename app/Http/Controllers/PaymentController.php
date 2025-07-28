<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="API endpoints for user payments"
 * )
 */
class PaymentController extends Controller
{
    /**
     * Display user's payments
     * 
     * @OA\Get(
     *     path="/api/payments",
     *     operationId="getUserPayments",
     *     tags={"Payments"},
     *     summary="Get user's payments",
     *     description="Returns a list of all payments for the authenticated user",
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/PaymentResource")
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        
        $query = Payment::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['order.shippingAddress.country', 'order.shippingAddress.province', 'order.shippingAddress.city'])
        ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->has('method')) {
            $query->where('method', $request->method);
        }

        $perPage = $request->get('per_page', 15);
        $payments = $query->paginate($perPage);

        return PaymentResource::collection($payments);
    }

    /**
     * Display the specified payment
     * 
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     operationId="getPayment",
     *     tags={"Payments"},
     *     summary="Get a specific payment",
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
     *             type="object",
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
    public function show(Request $request, Payment $payment): PaymentResource
    {
        $user = $request->user();
        
        // Check if the payment belongs to the authenticated user
        if ($payment->order->user_id !== $user->id) {
            abort(403, __('payments.not_authorized_to_view'));
        }

        $payment->load(['order.shippingAddress.country', 'order.shippingAddress.province', 'order.shippingAddress.city']);

        return new PaymentResource($payment);
    }
} 