<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MembershipTypeResource",
 *     title="Membership Type Resource",
 *     description="Membership type resource schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Basic Membership"),
 *     @OA\Property(property="description", type="string", example="Basic membership with standard benefits"),
 *     @OA\Property(property="price", type="number", format="decimal", example=50000),
 *     @OA\Property(property="formatted_price", type="string", example="50,000 ریال"),
 *     @OA\Property(property="day_cycle", type="integer", example=30),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 * )
 */
class MembershipTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'day_cycle' => $this->day_cycle,
            'is_active' => $this->is_active,
        ];
    }
} 