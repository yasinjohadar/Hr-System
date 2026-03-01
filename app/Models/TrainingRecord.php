<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'training_id',
        'employee_id',
        'status',
        'registration_date',
        'completion_date',
        'score',
        'feedback',
        'evaluation',
        'certificate_issued',
        'certificate_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'completion_date' => 'date',
        'certificate_date' => 'date',
        'score' => 'decimal:2',
        'certificate_issued' => 'boolean',
    ];

    /**
     * العلاقة مع الدورة التدريبية
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الحصول على حالة التسجيل بالعربية
     */
    public function getStatusArAttribute(): string
    {
        return match($this->status) {
            'registered' => 'مسجل',
            'attending' => 'يحضر',
            'completed' => 'مكتمل',
            'failed' => 'فاشل',
            'cancelled' => 'ملغي',
            default => $this->status
        };
    }

    /**
     * الحصول على تقييم النتيجة
     */
    public function getScoreRatingAttribute(): string
    {
        if (!$this->score) {
            return '-';
        }

        if ($this->score >= 90) {
            return 'ممتاز';
        } elseif ($this->score >= 80) {
            return 'جيد جداً';
        } elseif ($this->score >= 70) {
            return 'جيد';
        } elseif ($this->score >= 60) {
            return 'مقبول';
        } else {
            return 'ضعيف';
        }
    }
}
