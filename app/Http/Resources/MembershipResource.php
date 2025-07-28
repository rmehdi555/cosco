<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MembershipResource",
 *     title="Membership Resource",
 *     description="Membership resource schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="serial_number", type="string", example="MEM-001-2024"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-02-01"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="status_badge", type="string", example="فعال"),
 *     @OA\Property(property="remaining_days", type="integer", example=15),
 *     @OA\Property(property="duration", type="string", example="1 month"),
 *     @OA\Property(
 *         property="membership_type",  
 *         ref="#/components/schemas/MembershipTypeResource"
 *     ),
 * )
 */
class MembershipResource extends JsonResource
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
            'serial_number' => $this->serial_number,
            'user_id' => $this->user_id,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'is_active' => $this->is_active,
            'status_badge' => $this->status_badge,
            'remaining_days' => $this->remaining_days,
            'duration' => $this->duration,
            'membership_type' => new MembershipTypeResource($this->membershipType),
        ];
    }
} 