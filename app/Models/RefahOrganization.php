<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefahOrganization extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'refah_organizations';

    protected $fillable = [
        'title',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function refahUsers()
    {
        return $this->hasMany(RefahUser::class);
    }
}
