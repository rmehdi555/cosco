<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'is_show',
        'description',
        'image_url',
        'seo_title',
        'seo_description',
        'seo_follow',
        'seo_index',
        'seo_canonical',
    ];

    protected $casts = [
        'is_show' => 'boolean',
        'seo_follow' => 'boolean',
        'seo_index' => 'boolean',
    ];

    public function articles()
    {
        return $this->hasMany(\App\Models\Article::class, 'category_id');
    }
}
