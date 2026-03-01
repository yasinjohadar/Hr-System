<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period',
        'review_date',
        'period_start_date',
        'period_end_date',
        'job_knowledge',
        'work_quality',
        'productivity',
        'communication',
        'teamwork',
        'initiative',
        'problem_solving',
        'attendance_punctuality',
        'overall_rating',
        'strengths',
        'weaknesses',
        'goals_achieved',
        'future_goals',
        'comments',
        'employee_comments',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'review_date' => 'date',
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'job_knowledge' => 'integer',
        'work_quality' => 'integer',
        'productivity' => 'integer',
        'communication' => 'integer',
        'teamwork' => 'integer',
        'initiative' => 'integer',
        'problem_solving' => 'integer',
        'attendance_punctuality' => 'integer',
        'overall_rating' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * العلاقة مع الموظف المقيّم
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * العلاقة مع المقيّم (المدير)
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    /**
     * العلاقة مع من وافق
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * حساب التقييم الإجمالي تلقائياً
     */
    public function calculateOverallRating(): void
    {
        $ratings = [
            $this->job_knowledge,
            $this->work_quality,
            $this->productivity,
            $this->communication,
            $this->teamwork,
            $this->initiative,
            $this->problem_solving,
            $this->attendance_punctuality,
        ];

        // حساب المتوسط
        $sum = array_sum($ratings);
        $count = count(array_filter($ratings, fn($r) => $r > 0));
        
        $this->overall_rating = $count > 0 ? round($sum / $count, 2) : 0;
    }

    /**
     * الحصول على حالة التقييم بالعربية
     */
    public function getStatusArAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'completed' => 'مكتمل',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            default => $this->status
        };
    }

    /**
     * الحصول على التقييم الإجمالي كنص
     */
    public function getOverallRatingTextAttribute(): string
    {
        if ($this->overall_rating >= 4.5) {
            return 'ممتاز';
        } elseif ($this->overall_rating >= 3.5) {
            return 'جيد جداً';
        } elseif ($this->overall_rating >= 2.5) {
            return 'جيد';
        } elseif ($this->overall_rating >= 1.5) {
            return 'مقبول';
        } else {
            return 'ضعيف';
        }
    }
}
