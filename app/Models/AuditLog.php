<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'severity',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function getSeverityNameArAttribute(): string
    {
        return match($this->severity) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج',
            default => $this->severity,
        };
    }

    public function getActionNameArAttribute(): string
    {
        return match($this->action) {
            'create' => 'إنشاء',
            'update' => 'تحديث',
            'delete' => 'حذف',
            'view' => 'عرض',
            'login' => 'تسجيل دخول',
            'logout' => 'تسجيل خروج',
            'approve' => 'موافقة',
            'reject' => 'رفض',
            default => $this->action,
        };
    }
}
