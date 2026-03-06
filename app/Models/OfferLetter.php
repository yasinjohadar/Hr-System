<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferLetter extends Model
{
    protected $fillable = [
        'job_application_id',
        'job_title',
        'salary',
        'currency_id',
        'start_date',
        'valid_until',
        'status',
        'document_path',
        'notes',
        'rejection_reason',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'created_by',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'start_date' => 'date',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function getStatusNameArAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_SENT => 'مرسل',
            self::STATUS_ACCEPTED => 'مقبول',
            self::STATUS_REJECTED => 'مرفوض',
            default => $this->status,
        };
    }

    /**
     * Access candidate via job application.
     */
    public function getCandidateAttribute()
    {
        return $this->jobApplication?->candidate;
    }

    /**
     * Access job vacancy via job application.
     */
    public function getJobVacancyAttribute()
    {
        return $this->jobApplication?->jobVacancy;
    }
}
