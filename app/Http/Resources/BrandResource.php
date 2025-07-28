<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BrandResource",
 *     title="Brand Resource",
 *     description="Brand resource schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="اپل"),
 *     @OA\Property(property="slug", type="string", example="apple"),
 *     @OA\Property(property="image_url", type="string", example="https://example.com/brands/apple.png"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ProductResource")
 *     ),
 * )
 */
class BrandResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'is_active' => $this->is_active,
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
} 