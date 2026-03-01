<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'symbol',
        'symbol_ar',
        'decimal_places',
        'exchange_rate',
        'is_base_currency',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'exchange_rate' => 'decimal:4',
        'is_base_currency' => 'boolean',
        'is_active' => 'boolean',
    ];
}
