<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seo extends Model
{
    use SoftDeletes;
    protected $table = 'seo';

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'title',
        'description',
        'keyword',
        'schema_markup',
        'header_script',
    ];
}
