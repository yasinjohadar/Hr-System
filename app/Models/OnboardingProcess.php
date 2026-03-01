<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class OnboardingProcess extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'process_code',
        'employee_id',
        'template_id',
        'start_date',
        'expected_completion_date',
        'actual_completion_date',
        'status',
        'completion_percentage',
        'notes',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'completion_percentage' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($process) {
            if (empty($process->process_code)) {
                $process->process_code = 'ONB-' . strtoupper(Str::random(8));
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(OnboardingChecklist::class, 'process_id')->orderBy('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'لم يبدأ',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'on_hold' => 'معلق',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}
