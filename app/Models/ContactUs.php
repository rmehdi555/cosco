<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contact_us';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'body',
        'is_answered',
    ];

    protected $casts = [
        'is_answered' => 'boolean',
    ];

    /**
     * Get the full name of the contact
     */
    public function getFullNameAttribute(): string
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';
        
        return trim($firstName . ' ' . $lastName) ?: 'نامشخص';
    }

    /**
     * Scope for answered contacts
     */
    public function scopeAnswered($query)
    {
        return $query->where('is_answered', true);
    }

    /**
     * Scope for unanswered contacts
     */
    public function scopeUnanswered($query)
    {
        return $query->where('is_answered', false);
    }
}
