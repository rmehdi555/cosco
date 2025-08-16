<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="AddressResource",
 *   type="object",
 *   title="Address Resource",
 *   description="User address resource representation",
 *   @OA\Property(property="postal_code", type="string", example="1234567890", description="Postal code"),
 *   @OA\Property(property="plaque", type="string", example="12", description="Building plaque number"),
 *   @OA\Property(property="address", type="string", example="خیابان انقلاب، پلاک 12", description="Full address"),
 *   @OA\Property(property="phone", type="string", example="02112345678", description="Phone number"),
 *   @OA\Property(property="is_default", type="boolean", example=true, description="Is default address"),
 *   @OA\Property(property="province_title", type="string", example="تهران", description="Province name in Persian"),
 *   @OA\Property(property="province_id", type="integer", example=10, description="Province ID"),
 *   @OA\Property(property="city_title", type="string", example="تهران", description="City name in Persian"),
 *   @OA\Property(property="city_id", type="integer", example=100, description="City ID")
 * )
 */
class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'postal_code' => $this->postal_code,
            'plaque' => $this->plaque,
            'address' => $this->address,
            'phone' => $this->phone,
            'is_default' => $this->is_default,
            'province_title' => $this->province->title_fa,
            'province_id' => $this->province_id,
            'city_title' => $this->city->title_fa,
            'city_id' => $this->city_id,
        ];
    }
} 