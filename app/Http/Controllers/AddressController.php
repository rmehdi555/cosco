<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Responses\ApiResponse;

class AddressController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/addresses",
     *   summary="Get all addresses for the authenticated user",
     *   tags={"Address"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="List of addresses",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AddressResource"))
     *   )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->get();
        return AddressResource::collection($addresses);
    }

    /**
     * @OA\Post(
     *   path="/api/addresses",
     *   summary="Create a new address for the authenticated user",
     *   tags={"Address"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/StoreAddressRequest")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Address created",
     *     @OA\JsonContent(ref="#/components/schemas/AddressResource")
     *   )
     * )
     */
    public function store(StoreAddressRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $address = Address::create($data);
        return new AddressResource($address);
    }

    /**
     * @OA\Put(
     *   path="/api/addresses/{id}",
     *   summary="Update an address for the authenticated user",
     *   tags={"Address"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Address ID",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/UpdateAddressRequest")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Address updated",
     *     @OA\JsonContent(ref="#/components/schemas/AddressResource")
     *   )
     * )
     */
    public function update(UpdateAddressRequest $request, $id)
    {
        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->find($id);
        if (!$address) {
            return ApiResponse::notFound(trans('validation.address_not_found_or_forbidden'));
        }
        $data = $request->validated();
        $address->update($data);
        return new AddressResource($address);
    }
} 