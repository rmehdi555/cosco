<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PaymentResource",
 *     title="Payment Resource",
 *     description="Payment resource schema with all payment details",
 *     @OA\Property(property="id", type="integer", example=1, description="Payment ID"),
 *     @OA\Property(property="order_id", type="integer", example=1, description="Order ID"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="User ID"),
 *     @OA\Property(property="method", type="string", example="online", description="Payment method"),
 *     @OA\Property(property="method_label", type="string", example="Online", description="Payment method label"),
 *     @OA\Property(property="status", type="string", example="completed", description="Payment status"),
 *     @OA\Property(property="status_label", type="string", example="Completed", description="Payment status label"),
 *     @OA\Property(property="paid_at", type="string", format="date-time", example="2024-01-15T10:30:00Z", description="Payment completion date"),
 *     @OA\Property(property="amount", type="number", format="decimal", example=150000, description="Payment amount"),
 *     @OA\Property(property="formatted_amount", type="string", example="150,000 Rials", description="Formatted payment amount"),
 *     @OA\Property(property="merchant_id", type="string", example="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx", description="Merchant ID from gateway"),
 *     @OA\Property(property="bank_transaction_id", type="string", example="A000000000000000000000000000000000000", description="Bank transaction ID"),
 *     @OA\Property(property="bank_reference_id", type="string", example="123456789", description="Bank reference ID"),
 *     @OA\Property(property="description", type="string", example="Payment for order #123", description="Payment description"),
 *     @OA\Property(property="callback_url", type="string", example="https://example.com/payment/callback", description="Callback URL"),
 *     @OA\Property(property="gateway_response", type="object", description="Gateway response data"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:00:00Z", description="Payment creation date"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00Z", description="Payment last update date"),
 *     @OA\Property(
 *         property="order",
 *         ref="#/components/schemas/OrderResource",
 *         description="Related order information"
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
            'user_id' => $this->user_id,
            'method' => $this->method?->value,
            'method_label' => $this->method?->label(),
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'paid_at' => $this->paid_at?->toISOString(),
            'amount' => $this->amount,
            'formatted_amount' => number_format($this->amount) . ' Rials',
            'merchant_id' => $this->merchant_id,
            'bank_transaction_id' => $this->bank_transaction_id,
            'bank_reference_id' => $this->bank_reference_id,
            'description' => $this->description,
            'callback_url' => $this->callback_url,
            'gateway_response' => $this->gateway_response_array,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
} 