<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="UserResource",
 *   type="object",
 *   title="User Resource",
 *   description="User resource representation",
 *   @OA\Property(property="first_name", type="string", example="علی"),
 *   @OA\Property(property="last_name", type="string", example="احمدی"),
 *   @OA\Property(property="email", type="string", format="email", example="ali@example.com"),
 *   @OA\Property(property="cell_phone", type="string", example="09123456789"),
 *   @OA\Property(property="avatar_image_url", type="string", format="url", example="http://localhost:8000/storage/avatars/avatar.jpg"),
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'first_name' => $this->first_name ?? '',
            'last_name' => $this->last_name ?? '',
            'email' => $this->email,
            'cell_phone' => $this->cell_phone,
            'avatar_image_url' => $this->avatar_image ? asset('storage/' . $this->avatar_image) : '/assets/images/icons/user-default-icon.jpg',
        ];
    }
}
