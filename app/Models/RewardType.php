<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'description',
        'type',
        'default_value',
        'default_points',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'default_value' => 'decimal:2',
        'default_points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function employeeRewards(): HasMany
    {
        return $this->hasMany(EmployeeReward::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'monetary' => 'نقدي',
            'non_monetary' => 'غير نقدي',
            'points' => 'نقاط',
            'recognition' => 'اعتراف',
            'gift' => 'هدية',
            default => $this->type,
        };
    }
}
