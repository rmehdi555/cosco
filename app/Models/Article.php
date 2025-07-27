<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'is_show',
        'image_url',
        'view_count',
        'is_future',
        'seo_title',
        'seo_description',
        'seo_follow',
        'seo_index',
        'seo_canonical',
    ];

    protected $casts = [
        'is_show' => 'boolean',
        'is_future' => 'boolean',
        'seo_follow' => 'boolean',
        'seo_index' => 'boolean',
        'view_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
