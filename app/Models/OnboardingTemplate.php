<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'type',
        'steps',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'steps' => 'array',
        'is_active' => 'boolean',
    ];

    public function processes(): HasMany
    {
        return $this->hasMany(OnboardingProcess::class, 'template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'standard' => 'قياسي',
            'executive' => 'تنفيذي',
            'contractor' => 'مقاول',
            'intern' => 'متدرّب',
            'custom' => 'مخصص',
            default => $this->type ?? 'غير محدد',
        };
    }
}
