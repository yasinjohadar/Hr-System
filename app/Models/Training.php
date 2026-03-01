<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Training extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_ar',
        'code',
        'description',
        'description_ar',
        'type',
        'provider',
        'location',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'duration_hours',
        'max_participants',
        'cost',
        'currency_id',
        'status',
        'instructor_id',
        'objectives',
        'content',
        'materials',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_hours' => 'integer',
        'max_participants' => 'integer',
        'cost' => 'decimal:2',
        'certificate_issued' => 'boolean',
    ];

    /**
     * العلاقة مع العملة
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * العلاقة مع المدرب
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'instructor_id');
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع سجلات التدريب
     */
    public function trainingRecords(): HasMany
    {
        return $this->hasMany(TrainingRecord::class);
    }

    /**
     * العلاقة مع الموظفين (من خلال سجلات التدريب)
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'training_records')
            ->withPivot('status', 'registration_date', 'completion_date', 'score', 'feedback', 'evaluation', 'certificate_issued', 'certificate_date', 'notes')
            ->withTimestamps();
    }

    /**
     * الحصول على عدد المشاركين
     */
    public function getParticipantsCountAttribute(): int
    {
        return $this->trainingRecords()->count();
    }

    /**
     * الحصول على عدد المشاركين المكتملين
     */
    public function getCompletedCountAttribute(): int
    {
        return $this->trainingRecords()->where('status', 'completed')->count();
    }

    /**
     * التحقق من إمكانية التسجيل
     */
    public function canRegister(): bool
    {
        if ($this->status != 'planned' && $this->status != 'ongoing') {
            return false;
        }

        if ($this->max_participants && $this->participants_count >= $this->max_participants) {
            return false;
        }

        return true;
    }

    /**
     * الحصول على نوع التدريب بالعربية
     */
    public function getTypeArAttribute(): string
    {
        return match($this->type) {
            'internal' => 'داخلي',
            'external' => 'خارجي',
            'online' => 'أونلاين',
            'workshop' => 'ورشة عمل',
            default => $this->type
        };
    }

    /**
     * الحصول على حالة التدريب بالعربية
     */
    public function getStatusArAttribute(): string
    {
        return match($this->status) {
            'planned' => 'مخطط',
            'ongoing' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status
        };
    }
}
