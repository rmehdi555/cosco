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
    ];

    protected $casts = [
        'is_show' => 'boolean',
    ];

    // File relationship removed as it's not in the migration

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

}
