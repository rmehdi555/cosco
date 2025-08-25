<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CartResource",
 *     title="Cart Resource",
 *     description="Cart resource schema",
 *     @OA\Property(property="status", type="string", example="در انتظار", description="Cart status label in Persian"),
 *     @OA\Property(property="total_amount", type="string", example="15,000 تومان", description="Formatted total amount with currency"),
 *     @OA\Property(
 *         property="received_at",
 *         type="array",
 *         description="Available delivery dates",
 *         @OA\Items(type="string", example="2024-01-15")
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
        $dates = collect(range(2, 4))->map(fn($day) => config('general.show_date')(Carbon::now()->addDays($day)->toDateString()));

        return [
            'status' => $this->status?->getLabel(),
            'total_amount' => config('general.show_price')($this->total_amount),
            'received_at' => $dates
        ];
    }
}
