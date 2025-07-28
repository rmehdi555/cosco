<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="BrandResource",
 *   type="object",
 *   title="Brand Resource",
 *   description="Brand resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="سامسونگ"),
 *   @OA\Property(property="slug", type="string", example="samsung"),
 *   @OA\Property(property="image_url", type="string", example="/storage/brands/samsung.jpg"),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 * )
 */
class BrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'is_active' => $this->is_active,
        ];
    }
} 