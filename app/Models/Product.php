<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];
} 