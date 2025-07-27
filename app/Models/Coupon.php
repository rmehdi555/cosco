<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'discount_percent',
        'discount_amount',
        'expires_at',
        'max_uses',
        'used_count',
    ];

    protected $casts = [
        'discount_percent' => 'integer',
        'discount_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'max_uses' => 'integer',
        'used_count' => 'integer',
    ];
} 