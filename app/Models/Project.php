<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_code',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'department_id',
        'manager_id',
        'start_date',
        'end_date',
        'status',
        'priority',
        'budget',
        'currency_id',
        'progress',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'progress' => 'integer',
    ];

    /**
     * Boot method لتوليد رقم المشروع تلقائياً
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->project_code)) {
                $project->project_code = 'PRJ-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * العلاقة مع القسم
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * العلاقة مع مدير المشروع
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * العلاقة مع العملة
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * العلاقة مع المهام
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function memberEmployees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(ProjectTimeEntry::class);
    }

    /**
     * هل يمكن للموظف عرض المشروع أو تسجيل وقت عليه؟
     */
    public function employeeCanParticipate(Employee $employee): bool
    {
        if ($this->manager_id === $employee->id) {
            return true;
        }

        if ($this->members()->where('employee_id', $employee->id)->exists()) {
            return true;
        }

        return $this->tasks()
            ->whereHas('assignments', fn ($q) => $q->where('employee_id', $employee->id))
            ->exists();
    }

    public function allowsTimeLogging(): bool
    {
        return in_array($this->status, ['planning', 'active', 'on_hold'], true);
    }

    /**
     * العلاقة مع منشئ المشروع
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'planning' => 'قيد التخطيط',
            'active' => 'نشط',
            'on_hold' => 'معلق',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    /**
     * Accessor للأولوية بالعربية
     */
    public function getPriorityNameArAttribute(): string
    {
        return match($this->priority) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
            default => $this->priority,
        };
    }
}
