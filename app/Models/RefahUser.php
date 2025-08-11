<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefahUser extends Model
{
    use HasFactory;

    protected $table = 'refah_users';

    protected $fillable = [
        'first_name',
        'last_name',
        'cell_phone',
        'national_code',
        'birth_date',
        'gender',
        'number_of_family_members',
        'country_id',
        'province_id',
        'city_id',
        'postal_code',
        'phone',
        'job',
        'income',
        'refah_cart_id',
        'how_to_receive',
        'payment_method',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'income' => 'decimal:2',
        'number_of_family_members' => 'integer',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function refahCart()
    {
        return $this->belongsTo(RefahCart::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
