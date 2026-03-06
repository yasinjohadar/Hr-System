<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Policy extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'effective_date',
        'version',
        'is_active',
        'document_path',
        'created_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Policy $policy) {
            if (empty($policy->slug)) {
                $policy->slug = Str::slug($policy->title);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function acknowledgments(): HasMany
    {
        return $this->hasMany(PolicyAcknowledgment::class);
    }

    /**
     * الموظفون الذين اعترفوا بهذه السياسة (عبر الجدول الوسيط)
     */
    public function employees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'policy_acknowledgments')
            ->withPivot('acknowledged_at', 'ip_address')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
