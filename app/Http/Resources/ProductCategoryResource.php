<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ProductCategoryResource",
 *   type="object",
 *   title="Product Category Resource",
 *   description="Product category resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *   @OA\Property(property="name", type="string", example="کالای دیجیتال"),
 *   @OA\Property(property="slug", type="string", example="digital-goods"),
 *   @OA\Property(property="image_url", type="string", example="/storage/categories/digital.jpg"),
 *   @OA\Property(property="description", type="string", example="توضیحات دسته"),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 *   @OA\Property(property="children", type="array", @OA\Items(ref="#/components/schemas/ProductCategoryResource")),
 * )
 */
class ProductCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'children' => ProductCategoryResource::collection($this->whenLoaded('children')),
        ];
    }
} 