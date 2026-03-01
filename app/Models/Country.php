<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'code3',
        'phone_code',
        'currency_code',
        'flag',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع الموظفين
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'country');
    }

    /**
     * العلاقة مع الفروع
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'country');
    }
}
