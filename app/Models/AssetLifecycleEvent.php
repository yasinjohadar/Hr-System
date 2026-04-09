<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetLifecycleEvent extends Model
{
    protected $fillable = [
        'asset_id',
        'event_type',
        'occurred_at',
        'user_id',
        'employee_id',
        'related_assignment_id',
        'related_maintenance_id',
        'summary',
        'meta',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'meta' => 'array',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(AssetAssignment::class, 'related_assignment_id');
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(AssetMaintenance::class, 'related_maintenance_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AssetLifecycleAttachment::class);
    }

    public function getEventTypeNameArAttribute(): string
    {
        return match ($this->event_type) {
            'created' => 'إنشاء الأصل',
            'status_changed' => 'تغيير الحالة',
            'branch_changed' => 'تغيير الفرع',
            'department_changed' => 'تغيير القسم',
            'photo_updated' => 'تحديث صورة الأصل',
            'assignment_started' => 'بدء التوزيع / التسليم',
            'assignment_returned' => 'استرجاع من الموظف',
            'maintenance_recorded' => 'تسجيل صيانة',
            'maintenance_status_changed' => 'تغيير حالة الصيانة',
            'manual_note' => 'ملاحظة يدوية',
            default => $this->event_type,
        };
    }
}
