<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="CountryResource",
 *   type="object",
 *   title="Country Resource",
 *   description="Country resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="title_fa", type="string", example="ایران"),
 *   @OA\Property(property="title_en", type="string", example="Iran"),
 *   @OA\Property(property="provinces", type="array", @OA\Items(ref="#/components/schemas/ProvinceResource")),
 * )
 */
class CountryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title_fa' => $this->title_fa,
            'title_en' => $this->title_en,
            'provinces' => ProvinceResource::collection($this->whenLoaded('provinces')),
        ];
    }
} 