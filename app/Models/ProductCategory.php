<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'image_url',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function sliders()
    {
        return $this->hasMany(Slider::class, 'type', 'slug');
    }

    public function getBreadcrumb()
    {
        $breadcrumbs = collect([]);
        $category = $this;

        while ($category) {
            $breadcrumbs->prepend([
                'name' => $category->name,
                'slug' => $category->slug,
            ]);
            $category = $category->parent;
        }

        return $breadcrumbs->values();
    }



}
