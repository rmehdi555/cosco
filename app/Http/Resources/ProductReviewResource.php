<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ProductReviewResource",
 *   type="object",
 *   title="Product Review Resource",
 *   description="Product review resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="user_id", type="integer", example=2),
 *   @OA\Property(property="product_id", type="integer", example=3),
 *   @OA\Property(property="rating", type="integer", example=5),
 *   @OA\Property(property="comment", type="string", example="عالی بود!"),
 *   @OA\Property(property="approved", type="boolean", example=true),
 *   @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 * )
 */
class ProductReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user->first_name . ' ' . $this->user->last_name,
            'product_id' => $this->product_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'approved' => $this->approved,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'files' => [
                'https://api.rdst.ca/storage/product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg'
            ]
        ];
    }
}
