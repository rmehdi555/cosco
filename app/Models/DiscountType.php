<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountType extends Model
{
    use softDeletes;

    protected $fillable = [
        'product_category_id',
        'title',
        'footer',
        'background_color_up',
        'background_color_down',
        'image_url',
        'link',
        'target',
        'ads_title',
        'ads_footer',
        'ads_image_url',
        'ads_link',
        'ads_target',
        'is_show',
    ];

    protected $casts = [
        'is_show' => 'boolean',
        'target' => 'boolean',
        'ads_target' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
