<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefahCart extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'refah_cart';

    protected $fillable = [
        'title',
        'description',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function refahUsers()
    {
        return $this->hasMany(RefahUser::class);
    }
}
