<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SuccessionPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plan_code',
        'position_id',
        'current_employee_id',
        'description',
        'urgency',
        'target_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'target_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($plan) {
            if (empty($plan->plan_code)) {
                $plan->plan_code = 'SP-' . strtoupper(Str::random(8));
            }
        });
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function currentEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'current_employee_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(SuccessionCandidate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUrgencyNameArAttribute(): string
    {
        return match($this->urgency) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج',
            default => $this->urgency,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'planning' => 'قيد التخطيط',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}
