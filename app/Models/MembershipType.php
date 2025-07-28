<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipType extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'day_cycle',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'day_cycle' => 'integer',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0) . ' ریال';
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'فعال' : 'غیرفعال';
    }
} 