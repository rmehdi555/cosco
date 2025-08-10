<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="SliderResource",
 *   type="object",
 *   title="Slider Resource",
 *   description="Slider resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="title", type="string", example="عنوان اسلایدر"),
 *   @OA\Property(property="link", type="string", example="https://example.com"),
 *   @OA\Property(property="image_url", type="string", example="http://localhost:8000/storage/sliders/slider.jpg"),
 *   @OA\Property(property="target", type="string", example="_blank")
 * )
 */
class SliderResource extends JsonResource
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
            'title' => $this->title,
            'link' => $this->link,
            'image_url' => asset('storage/' . $this->image_url),
            'target' => $this->target,
        ];
    }
}
