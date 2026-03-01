<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeGoal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'performance_review_id',
        'title',
        'description',
        'type',
        'priority',
        'start_date',
        'target_date',
        'completion_date',
        'status',
        'progress_percentage',
        'success_criteria',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_date' => 'date',
        'completion_date' => 'date',
        'progress_percentage' => 'integer',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع تقييم الأداء
     */
    public function performanceReview(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class);
    }

    /**
     * العلاقة مع منشئ الهدف
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor لنوع الهدف بالعربية
     */
    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'personal' => 'شخصي',
            'team' => 'فريق',
            'department' => 'قسم',
            'company' => 'شركة',
            default => $this->type,
        };
    }

    /**
     * Accessor للأولوية بالعربية
     */
    public function getPriorityNameArAttribute(): string
    {
        return match($this->priority) {
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'critical' => 'حرجة',
            default => $this->priority,
        };
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'لم يبدأ',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'on_hold' => 'معلق',
            default => $this->status,
        };
    }

    /**
     * تحديث نسبة التقدم
     */
    public function updateProgress(int $percentage)
    {
        $this->update([
            'progress_percentage' => min(100, max(0, $percentage)),
            'status' => $percentage >= 100 ? 'completed' : ($percentage > 0 ? 'in_progress' : 'not_started'),
            'completion_date' => $percentage >= 100 ? now() : null,
        ]);
    }
}
