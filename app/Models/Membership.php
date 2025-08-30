<?php

namespace App\Models;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'serial_number',
        'user_id',
        'start_date',
        'end_date',
        'is_active',
        'membership_type_id',
        'payment_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'status' => OrderStatus::class,
        'payment_status' => OrderPaymentStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_active) {
            return 'غیرفعال';
        }

        if ($this->end_date->isPast()) {
            return 'منقضی شده';
        }

        if ($this->start_date->isFuture()) {
            return 'در انتظار شروع';
        }

        return 'فعال';
    }

    public function getDurationAttribute(): string
    {
        return $this->start_date->diffForHumans($this->end_date, true);
    }

    public function getRemainingDaysAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function paymentMembership(): HasMany
    {
        return $this->hasMany(PaymentMembership::class);
    }
}
