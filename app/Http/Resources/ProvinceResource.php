<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ProvinceResource",
 *   type="object",
 *   title="Province Resource",
 *   description="Province resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="country_id", type="integer", example=1),
 *   @OA\Property(property="title_fa", type="string", example="تهران"),
 *   @OA\Property(property="title_en", type="string", example="Tehran"),
 *   @OA\Property(property="country", ref="#/components/schemas/CountryResource"),
 *   @OA\Property(property="cities", type="array", @OA\Items(ref="#/components/schemas/CityResource")),
 * )
 */
class ProvinceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'title_fa' => $this->title_fa,
            'title_en' => $this->title_en,
            'country' => new CountryResource($this->whenLoaded('country')),
            'cities' => CityResource::collection($this->whenLoaded('cities')),
        ];
    }
} 