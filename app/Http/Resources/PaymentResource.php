<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PaymentResource",
 *     title="Payment Resource",
 *     description="Payment resource schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="method", type="string", example="online"),
 *     @OA\Property(property="method_label", type="string", example="آنلاین"),
 *     @OA\Property(property="status", type="string", example="completed"),
 *     @OA\Property(property="status_label", type="string", example="تکمیل شده"),
 *     @OA\Property(property="paid_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
 *     @OA\Property(property="amount", type="number", format="decimal", example=150000),
 *     @OA\Property(property="formatted_amount", type="string", example="150,000 ریال"),
 *     @OA\Property(
 *         property="order",
 *         ref="#/components/schemas/OrderResource"
 *     ),
 * )
 */
class PaymentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'method' => $this->method?->value,
            'method_label' => $this->method?->label(),
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'paid_at' => $this->paid_at?->toISOString(),
            'amount' => $this->amount,
            'formatted_amount' => number_format($this->amount) . ' ریال',
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
} 