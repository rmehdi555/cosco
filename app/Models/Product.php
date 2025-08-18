<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'is_featured',
        'is_online_only',
        'body',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_online_only' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function mainImage(): BelongsTo
    {
        return $this->belongsTo(ProductImage::class, 'id', 'product_id')->where('is_main', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(\App\Models\ProductView::class);
    }

    public function typeBuy()
    {
        if ($this->is_online_only == true) {
            $type_buy = [[
                'text' => 'خرید انلاین',
                'bg_color' => '#005dab',
            ]];
        } else {
            $type_buy = [
                [
                    'text' => 'خرید انلاین',
                    'bg_color' => '#005dab',
                ],
                [
                    'text' => 'خرید حضوری',
                    'bg_color' => '#008000',
                ]
            ];
        }

        return $type_buy;
    }

    public function countRate()
    {
        return $this->reviews->count();
    }

    public function averageRate()
    {
        $count_rate = $this->countRate();
        $all_rates = $this->reviews->sum('rating');
        if ($count_rate == 0)
            $count_rate = 1;
        $average_rate = round($all_rates / $count_rate, 1);

        return $average_rate;
    }

    public function imagesArray()
    {
        $images = [];
        foreach ($this->images as $image) {
            $images[] = asset('storage/' . $image->image_url);
        }

        return $images;
    }
}
