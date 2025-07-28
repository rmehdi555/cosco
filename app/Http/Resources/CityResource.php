<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="CityResource",
 *   type="object",
 *   title="City Resource",
 *   description="City resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="province_id", type="integer", example=1),
 *   @OA\Property(property="title_fa", type="string", example="تهران"),
 *   @OA\Property(property="title_en", type="string", example="Tehran"),
 *   @OA\Property(property="province", ref="#/components/schemas/ProvinceResource"),
 * )
 */
class CityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'province_id' => $this->province_id,
            'title_fa' => $this->title_fa,
            'title_en' => $this->title_en,
            'province' => new ProvinceResource($this->whenLoaded('province')),
        ];
    }
} 