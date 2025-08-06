<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use softDeletes;

    protected $fillable = [
        'title',
        'link',
        'image_url',
        'type',
        'is_show',
        'target',
    ];

    protected $casts = [
        'is_show' => 'boolean',
        'target' => 'boolean',
    ];
}
