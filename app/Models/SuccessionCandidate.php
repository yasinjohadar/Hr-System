<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuccessionCandidate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'succession_plan_id',
        'employee_id',
        'readiness_level',
        'readiness_score',
        'strengths',
        'development_needs',
        'action_plan',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'readiness_score' => 'integer',
    ];

    public function successionPlan(): BelongsTo
    {
        return $this->belongsTo(SuccessionPlan::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getReadinessLevelNameArAttribute(): string
    {
        return match($this->readiness_level) {
            'ready_now' => 'جاهز الآن',
            'ready_1_year' => 'جاهز خلال سنة',
            'ready_2_years' => 'جاهز خلال سنتين',
            'ready_3_years' => 'جاهز خلال 3 سنوات',
            'not_ready' => 'غير جاهز',
            default => $this->readiness_level,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'potential' => 'محتمل',
            'identified' => 'محدد',
            'developing' => 'قيد التطوير',
            'ready' => 'جاهز',
            'selected' => 'مختار',
            'rejected' => 'مرفوض',
            default => $this->status,
        };
    }
}
