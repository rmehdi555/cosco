<?php

namespace App\Http\Controllers;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Requests\MemeberShipStoreRequest;
use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Services\OnlinePaymentMembershipService;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Memberships",
 *     description="API endpoints for user memberships"
 * )
 */
class MembershipController extends Controller
{
    protected OnlinePaymentMembershipService $paymentMembershipService;

    public function __construct(OnlinePaymentMembershipService $paymentMembershipService)
    {
        $this->paymentMembershipService = $paymentMembershipService;
    }

    /**
     * Display user's memberships
     *
     * @OA\Get(
     *     path="/api/memberships",
     *     operationId="getUserMemberships",
     *     tags={"Memberships"},
     *     summary="Get user's memberships",
     *     description="Returns a list of all memberships for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/MembershipResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $memberships = $user->memberships()
            ->with('membershipType')
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::success(MembershipResource::collection($memberships));
    }

    /**
     * Display user's active membership status
     *
     * @OA\Get(
     *     path="/api/memberships/status",
     *     operationId="getUserMembershipStatus",
     *     tags={"Memberships"},
     *     summary="Get user's active membership status",
     *     description="Returns the active membership status for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="has_active_membership", type="boolean", example=true),
     *                     @OA\Property(property="message", type="string", example="شما عضویت فعال دارید"),
     *                     @OA\Property(property="remaining_days", type="integer", example=15),
     *                     @OA\Property(
     *                         property="membership",
     *                         ref="#/components/schemas/MembershipResource"
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="has_active_membership", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="شما عضویت فعالی ندارید"),
     *                     @OA\Property(property="remaining_days", type="integer", example=0),
     *                     @OA\Property(property="membership", type="null")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="has_active_membership", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="عضویت شما منقضی شده است"),
     *                     @OA\Property(property="remaining_days", type="integer", example=0),
     *                     @OA\Property(
     *                         property="membership",
     *                         ref="#/components/schemas/MembershipResource"
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function status(Request $request)
    {
        $user = $request->user();

        $activeMembership = $user->active_membership;

        if (!$activeMembership) {
            return ApiResponse::success([
                'has_active_membership' => false,
                'message' => __('membership.no_active_membership'),
                'remaining_days' => 0,
                'membership' => null,
            ]);
        }

        // Check if membership has expired
        $remainingDays = $activeMembership->remaining_days;

        if ($remainingDays <= 0) {
            return ApiResponse::success([
                'has_active_membership' => false,
                'message' => __('membership.membership_expired_user'),
                'remaining_days' => 0,
                'membership' => new MembershipResource($activeMembership),
            ]);
        }

        return ApiResponse::success([
            'has_active_membership' => true,
            'message' => __('membership.has_active_membership'),
            'remaining_days' => $remainingDays,
            'membership' => new MembershipResource($activeMembership),
        ]);
    }

    /**
     * Display the specified membership
     *
     * @OA\Get(
     *     path="/api/memberships/{id}",
     *     operationId="getMembership",
     *     tags={"Memberships"},
     *     summary="Get a specific membership",
     *     description="Returns details of a specific membership (only user's own memberships)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Membership ID",
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
     *                 ref="#/components/schemas/MembershipResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to view this membership"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Membership not found"
     *     )
     * )
     */
    public function show(Request $request, Membership $membership)
    {
        // Check if the membership belongs to the authenticated user
        if ($membership->user_id !== $request->user()->id) {
            abort(403, __('membership.not_authorized_to_view'));
        }

        return ApiResponse::success(new MembershipResource($membership));
    }

    /**
     * Create a new membership
     *
     * @OA\Post(
     *     path="/api/membership",
     *     operationId="createMembership",
     *     tags={"Memberships"},
     *     summary="Create a new membership",
     *     description="Creates a new membership for the authenticated user and sends payment request to gateway",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"membership_type_id"},
     *             @OA\Property(
     *                 property="membership_type_id",
     *                 type="integer",
     *                 description="ID of the membership type",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="gateway",
     *                 type="string",
     *                 description="Payment gateway to use (optional)",
     *                 example="zarinpal_test"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Membership created successfully and payment gateway request sent",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="درخواست درگاه پرداخت با موفقیت ارسال شد"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="payment_id", type="integer", example=123),
     *                 @OA\Property(property="gateway_url", type="string", example="https://www.zarinpal.com/pg/StartPay/123456789"),
     *                 @OA\Property(property="transaction_id", type="string", example="123456789"),
     *                 @OA\Property(property="message", type="string", example="درخواست پرداخت با موفقیت ارسال شد")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Order already paid or validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="سفارش قبلاً پرداخت شده است"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Membership type not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="نوع عضویت یافت نشد"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The membership type id field is required."),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="خطا در ایجاد سفارش"),
     *             @OA\Property(property="data", type="string", example="Error details")
     *         )
     *     )
     * )
     */
    public function store(MemeberShipStoreRequest $request)
    {
        $membershipType = MembershipType::whereId($request->membership_type_id)->firstOrFail();
        $user_id = Auth::id();
        $serialNumber = Str::uuid()->toString();
        $dayCycle = $membershipType->day_cycle;

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays($dayCycle)->format('Y-m-d');

        try {
            DB::beginTransaction();
            $membership = Membership::create([
                'user_id' => $user_id,
                'serial_number' => $serialNumber,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'membership_type_id' => $membershipType->id,
                'status' => OrderStatus::PENDING,
                'payment_status' => OrderPaymentStatus::UNPAID,
            ]);

            // بررسی وضعیت پرداخت
            if ($membership->payment_status === 'paid') {
                return ApiResponse::error(trans('payments.order_already_paid'), null, 400);
            }

            $gateway = 'zarinpal_test';

            $result = $this->paymentMembershipService->sendToGateway($membership, $gateway);
            DB::commit();

            if ($result['success']) {
                return ApiResponse::success($result, trans('payments.gateway_request_sent_success'));
            } else {
                return ApiResponse::error($result['message'] ?? trans('payments.gateway_request_failed'), null, 500);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::serverError(
                __('orders.creation_failed'),
                $e->getMessage()
            );
        }
    }
}
