<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefahOrganization extends Model
{
    use HasFactory;

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
