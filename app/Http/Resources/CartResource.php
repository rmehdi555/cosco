<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CartResource",
 *     title="Cart Resource",
 *     description="Cart resource schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="status_label", type="string", example="در انتظار"),
 *     @OA\Property(property="total_amount", type="number", format="decimal", example=150000),
 *     @OA\Property(property="formatted_total_amount", type="string", example="150,000 ریال"),
 *     @OA\Property(
 *         property="cart_items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CartItemResource")
 *     )
 * )
 */
class CartResource extends JsonResource
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
            'user_id' => $this->user_id,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'total_amount' => $this->total_amount,
            'formatted_total_amount' => number_format($this->total_amount) . ' ریال',
        ];
    }
} 