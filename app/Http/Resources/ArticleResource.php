<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ArticleResource",
 *   type="object",
 *   title="Article Resource",
 *   description="Article resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="category_id", type="integer", example=2),
 *   @OA\Property(property="user_id", type="integer", example=1),
 *   @OA\Property(property="title", type="string", example="عنوان مقاله"),
 *   @OA\Property(property="slug", type="string", example="article-title"),
 *   @OA\Property(property="excerpt", type="string", example="خلاصه مقاله"),
 *   @OA\Property(property="body", type="string", example="متن کامل مقاله"),
 *   @OA\Property(property="is_show", type="boolean", example=true),
 *   @OA\Property(property="image_url", type="string", example="http://localhost:8000/storage/articles/article.jpg"),
 *   @OA\Property(property="view_count", type="integer", example=123),
 *   @OA\Property(property="is_future", type="boolean", example=false),
 *   @OA\Property(property="seo_title", type="string", example="سئو مقاله"),
 *   @OA\Property(property="seo_description", type="string", example="توضیحات سئو مقاله"),
 *   @OA\Property(property="seo_follow", type="boolean", example=true),
 *   @OA\Property(property="seo_index", type="boolean", example=true),
 *   @OA\Property(property="seo_canonical", type="string", example="https://example.com/article"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-27T12:34:56Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-27T12:34:56Z"),
 * )
 */
class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'is_show' => $this->is_show,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'view_count' => $this->view_count,
            'is_future' => $this->is_future,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_follow' => $this->seo_follow,
            'seo_index' => $this->seo_index,
            'seo_canonical' => $this->seo_canonical,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 