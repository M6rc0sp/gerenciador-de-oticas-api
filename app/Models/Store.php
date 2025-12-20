<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'nuvemshop_id',
        'access_token',
        'name',
        'email',
        'domain',
        'original_domain',
        'plan',
        'country',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'nuvemshop_id' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'access_token',
    ];
}
