<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     title="Order Resource",
 *     description="Order resource schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="status_label", type="string", example="در انتظار"),
 *     @OA\Property(property="total_amount", type="number", format="decimal", example=150000),
 *     @OA\Property(property="formatted_total_amount", type="string", example="150,000 ریال"),
 *     @OA\Property(property="payment_status", type="string", example="unpaid"),
 *     @OA\Property(property="payment_status_label", type="string", example="پرداخت نشده"),
 *     @OA\Property(
 *         property="shipping_address",
 *         ref="#/components/schemas/AddressResource"
 *     ),
 *     @OA\Property(
 *         property="order_items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderItemResource")
 *     ),
 *     @OA\Property(property="received_at", type="string", format="date-time", nullable=true, example="2024-01-15T10:30:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class OrderResource extends JsonResource
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
            'payment_status' => $this->payment_status?->value,
            'payment_status_label' => $this->payment_status?->label(),
            'shipping_address' => new AddressResource($this->shippingAddress),
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'received_at' => $this->received_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
} 