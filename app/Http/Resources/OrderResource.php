<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function Termwind\parse;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     title="Order Resource",
 *     description="Order resource schema",
 *     @OA\Property(property="id", type="integer", example=1, description="Order ID"),
 *     @OA\Property(property="status", type="string", example="در انتظار", description="Order status label in Persian"),
 *     @OA\Property(property="status_english", type="string", example="pending", description="Order status in English"),
 *     @OA\Property(property="total_amount", type="string", example="15,000 تومان", description="Formatted total amount with currency"),
 *     @OA\Property(property="formatted_total_amount", type="string", example="15,000 تومان", description="Formatted total amount with currency"),
 *     @OA\Property(property="payment_status", type="string", example="پرداخت نشده", description="Payment status label in Persian"),
 *     @OA\Property(property="payment_status_english", type="string", example="unpaid", description="Payment status in English"),
 *     @OA\Property(property="description", type="string", nullable=true, example="توضیحات مربوط به سفارش", description="Order description"),
 *     @OA\Property(
 *         property="shipping_address",
 *         ref="#/components/schemas/AddressResource",
 *         description="Shipping address details"
 *     ),
 *     @OA\Property(
 *         property="order_items",
 *         type="array",
 *         description="Array of order items",
 *         @OA\Items(ref="#/components/schemas/OrderItemResource")
 *     ),
 *     @OA\Property(property="received_at", type="string", nullable=true, example="1402-10-25", description="Order received date in Persian calendar"),
 *     @OA\Property(property="created_at", type="string", example="1402-10-25", description="Order creation date in Persian calendar")
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
        if ($this->payment_status == 'paid')
            $is_pay = true;
        else
            $is_pay = false;

        return [
            'id' => $this->id,
            'status' => $this->status?->getLabel(),
            'status_english' => $this->status,
            'total_amount' => config('general.show_price')($this->total_amount),
            'formatted_total_amount' => config('general.format_price')($this->total_amount),
            'payment_status' => $this->payment_status?->getLabel(),
            'payment_status_english' => $this->payment_status,
            'description' => $this->description,
            'is_pay' => $is_pay,
            'shipping_address' => new AddressResource($this->shippingAddress),
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'received_at' => config('general.show_date')($this->received_at) ?? '',
            'created_at' => config('general.show_date')($this->created_at),
        ];
    }
}
