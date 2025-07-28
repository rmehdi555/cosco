<?php

namespace App\Http\Controllers;

use App\Http\Resources\MembershipTypeResource;
use App\Models\MembershipType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Membership Types",
 *     description="API endpoints for membership types"
 * )
 */
class MembershipTypeController extends Controller
{
    /**
     * Display a listing of membership types
     * 
     * @OA\Get(
     *     path="/api/membership-types",
     *     operationId="getMembershipTypes",
     *     tags={"Membership Types"},
     *     summary="Get all active membership types",
     *     description="Returns a list of all active membership types ordered by price",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/MembershipTypeResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $membershipTypes = MembershipType::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        return MembershipTypeResource::collection($membershipTypes);
    }

    /**
     * Display the specified membership type
     * 
     * @OA\Get(
     *     path="/api/membership-types/{id}",
     *     operationId="getMembershipType",
     *     tags={"Membership Types"},
     *     summary="Get a specific membership type",
     *     description="Returns details of a specific membership type",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Membership type ID",
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
     *                 ref="#/components/schemas/MembershipTypeResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Membership type not found"
     *     )
     * )
     */
    public function show(MembershipType $membershipType): MembershipTypeResource
    {
        return new MembershipTypeResource($membershipType);
    }
} 