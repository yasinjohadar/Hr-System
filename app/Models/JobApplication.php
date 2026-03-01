<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_vacancy_id',
        'candidate_id',
        'application_date',
        'source',
        'referrer_name',
        'status',
        'rejection_reason',
        'rejection_date',
        'rating',
        'reviewer_notes',
        'cv_path',
        'cover_letter_path',
        'additional_documents',
        'notes',
        'expected_salary',
        'available_start_date',
        'reviewed_by',
        'created_by',
    ];

    protected $casts = [
        'application_date' => 'date',
        'rejection_date' => 'date',
        'available_start_date' => 'date',
        'rating' => 'integer',
        'expected_salary' => 'decimal:2',
        'additional_documents' => 'array',
    ];

    /**
     * العلاقة مع الوظيفة الشاغرة
     */
    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }

    /**
     * العلاقة مع المرشح
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * العلاقة مع المراجع
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع المقابلات
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'reviewing' => 'قيد المراجعة',
            'shortlisted' => 'قائمة مختصرة',
            'interviewed' => 'تمت المقابلة',
            'offered' => 'تم العرض',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'withdrawn' => 'انسحب',
            default => $this->status,
        };
    }

    /**
     * Accessor لمصدر التقديم بالعربية
     */
    public function getSourceNameArAttribute(): string
    {
        return match($this->source) {
            'website' => 'الموقع الإلكتروني',
            'linkedin' => 'لينكد إن',
            'referral' => 'إحالة',
            'indeed' => 'إنديد',
            'other' => 'أخرى',
            default => $this->source,
        };
    }
}
