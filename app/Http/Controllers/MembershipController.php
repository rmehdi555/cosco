<?php

namespace App\Http\Controllers;

use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Memberships",
 *     description="API endpoints for user memberships"
 * )
 */
class MembershipController extends Controller
{
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
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        
        $memberships = $user->memberships()
            ->with('membershipType')
            ->orderBy('created_at', 'desc')
            ->get();

        return MembershipResource::collection($memberships);
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
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $activeMembership = $user->active_membership;
        
        if (!$activeMembership) {
            return response()->json([
                'has_active_membership' => false,
                'message' => __('membership.no_active_membership'),
                'remaining_days' => 0,
                'membership' => null,
            ]);
        }

        // Check if membership has expired
        $remainingDays = $activeMembership->remaining_days;
        
        if ($remainingDays <= 0) {
            return response()->json([
                'has_active_membership' => false,
                'message' => __('membership.membership_expired_user'),
                'remaining_days' => 0,
                'membership' => new MembershipResource($activeMembership),
            ]);
        }

        return response()->json([
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
    public function show(Request $request, Membership $membership): MembershipResource
    {
        // Check if the membership belongs to the authenticated user
        if ($membership->user_id !== $request->user()->id) {
            abort(403, __('membership.not_authorized_to_view'));
        }

        return new MembershipResource($membership);
    }
} 