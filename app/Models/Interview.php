<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_application_id',
        'candidate_id',
        'job_vacancy_id',
        'title',
        'type',
        'round',
        'interview_date',
        'interview_time',
        'duration',
        'timezone',
        'location',
        'meeting_link',
        'address',
        'interviewers',
        'scheduled_by',
        'status',
        'cancellation_reason',
        'overall_rating',
        'technical_skills_rating',
        'communication_rating',
        'cultural_fit_rating',
        'strengths',
        'weaknesses',
        'recommendation',
        'recommendation_status',
        'interview_notes',
        'candidate_feedback',
        'questions_asked',
        'answers_given',
        'next_steps',
        'conducted_by',
        'created_by',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'interview_time' => 'datetime',
        'duration' => 'integer',
        'overall_rating' => 'integer',
        'interviewers' => 'array',
    ];

    /**
     * العلاقة مع طلب التوظيف
     */
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * العلاقة مع المرشح
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * العلاقة مع الوظيفة الشاغرة
     */
    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }

    /**
     * العلاقة مع من جدول المقابلة
     */
    public function scheduler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    /**
     * العلاقة مع من أجرى المقابلة
     */
    public function conductor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    /**
     * العلاقة مع منشئ السجل
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
            'scheduled' => 'مجدولة',
            'confirmed' => 'مؤكدة',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
            'rescheduled' => 'أعيد جدولتها',
            'no_show' => 'لم يحضر',
            default => $this->status,
        };
    }

    /**
     * Accessor لنوع المقابلة بالعربية
     */
    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'phone' => 'هاتفية',
            'video' => 'فيديو',
            'in_person' => 'شخصية',
            'panel' => 'لجنة',
            'technical' => 'تقنية',
            'hr' => 'موارد بشرية',
            'final' => 'نهائية',
            default => $this->type,
        };
    }

    /**
     * Accessor لجولة المقابلة بالعربية
     */
    public function getRoundNameArAttribute(): string
    {
        return match($this->round) {
            'first' => 'الأولى',
            'second' => 'الثانية',
            'third' => 'الثالثة',
            'final' => 'النهائية',
            default => $this->round,
        };
    }
}
