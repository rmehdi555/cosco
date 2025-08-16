<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReviewFile extends Model
{
    use SoftDeletes;

    protected $table = 'product_review_files';

    protected $fillable = [
        'product_review_id',
        'image_url'
    ];
}
