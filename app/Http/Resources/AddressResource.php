<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="AddressResource",
 *   type="object",
 *   title="Address Resource",
 *   description="User address resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="user_id", type="integer", example=2),
 *   @OA\Property(property="country_id", type="integer", example=1),
 *   @OA\Property(property="province_id", type="integer", example=10),
 *   @OA\Property(property="city_id", type="integer", example=100),
 *   @OA\Property(property="postal_code", type="string", example="1234567890"),
 *   @OA\Property(property="plaque", type="string", example="12"),
 *   @OA\Property(property="address", type="string", example="خیابان انقلاب، پلاک 12"),
 *   @OA\Property(property="phone", type="string", example="02112345678"),
 *   @OA\Property(property="is_default", type="boolean", example=true),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 *   @OA\Property(property="country", ref="#/components/schemas/CountryResource"),
 *   @OA\Property(property="province", ref="#/components/schemas/ProvinceResource"),
 *   @OA\Property(property="city", ref="#/components/schemas/CityResource"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-27T12:34:56Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-27T12:34:56Z"),
 * )
 */
class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'country_id' => $this->country_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'postal_code' => $this->postal_code,
            'plaque' => $this->plaque,
            'address' => $this->address,
            'phone' => $this->phone,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'country' => new CountryResource($this->whenLoaded('country')),
            'province' => new ProvinceResource($this->whenLoaded('province')),
            'city' => new CityResource($this->whenLoaded('city')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 