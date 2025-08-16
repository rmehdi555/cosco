<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ProvinceResource",
 *   type="object",
 *   title="Province Resource",
 *   description="Province resource representation",
 *   @OA\Property(property="title", type="string", example="تهران", description="Province name in Persian")
 * )
 */
class ProvinceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title' => $this->title_fa,
        ];
    }
} 