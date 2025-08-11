<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ShowWithProductResource",
 *   type="object",
 *   title="Show With Product Category Resource",
 *   description="Flattened category fields for listing alongside products",
 *   @OA\Property(property="id", type="integer", example=3),
 *   @OA\Property(property="name", type="string", example="موبایل و تبلت"),
 *   @OA\Property(property="slug", type="string", example="mobile-tablet"),
 *   @OA\Property(property="image_url", type="string", nullable=true, example="http://localhost:8000/storage/categories/mobile.jpg"),
 *   @OA\Property(property="description", type="string", nullable=true, example="توضیح کوتاه دسته")
 * )
 */
class ShowWithProductResource extends JsonResource
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
            'description' => $this->description,
        ];
    }
}
