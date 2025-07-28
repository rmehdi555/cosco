<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CartItemResource",
 *     title="Cart Item Resource",
 *     description="Cart item resource schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="cart_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="price", type="number", format="decimal", example=75000),
 *     @OA\Property(property="formatted_price", type="string", example="75,000 ریال"),
 *     @OA\Property(property="total_price", type="number", format="decimal", example=150000),
 *     @OA\Property(property="formatted_total_price", type="string", example="150,000 ریال"),
 *     @OA\Property(
 *         property="product",
 *         ref="#/components/schemas/ProductResource"
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class CartItemResource extends JsonResource
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
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'formatted_price' => number_format($this->price) . ' ریال',
            'total_price' => $this->total_price,
            'formatted_total_price' => number_format($this->total_price) . ' ریال',
            'product' => new ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
} 