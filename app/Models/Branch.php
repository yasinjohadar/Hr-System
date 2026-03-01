<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'city',
        'country',
        'postal_code',
        'phone',
        'email',
        'manager_name',
        'manager_id',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'working_days',
        'is_main',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'working_days' => 'array',
        'is_main' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع المدير
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع الموظفين
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
