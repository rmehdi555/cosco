<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'method',
        'status',
        'paid_at',
        'amount',
        'merchant_id',
        'bank_transaction_id',
        'bank_reference_id',
        'description',
        'callback_url',
        'gateway_response',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'status' => PaymentStatus::class,
        'method' => PaymentMethod::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted gateway response
     */
    public function getFormattedGatewayResponseAttribute(): ?string
    {
        if (!$this->gateway_response) {
            return null;
        }

        $response = json_decode($this->gateway_response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $this->gateway_response;
    }

    /**
     * Get gateway response as array
     */
    public function getGatewayResponseArrayAttribute(): ?array
    {
        if (!$this->gateway_response) {
            return null;
        }

        $response = json_decode($this->gateway_response, true);
        return json_last_error() === JSON_ERROR_NONE ? $response : null;
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::COMPLETED;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }
} 