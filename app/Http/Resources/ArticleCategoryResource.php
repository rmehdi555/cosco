<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ArticleResource;

/**
 * @OA\Schema(
 *   schema="ArticleCategoryResource",
 *   type="object",
 *   title="Article Category Resource",
 *   description="Article category resource representation",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="اخبار سایت"),
 *   @OA\Property(property="slug", type="string", example="news"),
 *   @OA\Property(property="is_show", type="boolean", example=true),
 *   @OA\Property(property="description", type="string", example="دسته اخبار سایت"),
 *   @OA\Property(property="image_url", type="string", example="http://localhost:8000/storage/articles/news.jpg"),
 *   @OA\Property(property="seo_title", type="string", example="سئو اخبار سایت"),
 *   @OA\Property(property="seo_description", type="string", example="توضیحات سئو اخبار سایت"),
 *   @OA\Property(property="seo_follow", type="boolean", example=true),
 *   @OA\Property(property="seo_index", type="boolean", example=true),
 *   @OA\Property(property="seo_canonical", type="string", example="https://example.com/news"),
 *   @OA\Property(property="articles", type="array", @OA\Items(ref="#/components/schemas/ArticleResource")),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-27T12:34:56Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-27T12:34:56Z"),
 * )
 */
class ArticleCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_show' => $this->is_show,
            'description' => $this->description,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_follow' => $this->seo_follow,
            'seo_index' => $this->seo_index,
            'seo_canonical' => $this->seo_canonical,
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 