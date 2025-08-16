<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CartResource",
 *     title="Cart Resource",
 *     description="Cart resource schema",
 *     @OA\Property(property="status", type="string", example="در انتظار", description="Cart status label in Persian"),
 *     @OA\Property(property="total_amount", type="number", format="decimal", example=15000, description="Total amount of cart in Toman")
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
            'status' => $this->status?->getLabel(),
            'total_amount' => config('general.show_price')($this->total_amount),

        ];
    }
} 