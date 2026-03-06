<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomNotification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'message',
        'message_ar',
        'user_id',
        'recipient_ids',
        'recipient_type',
        'action_url',
        'action_text',
        'icon',
        'color',
        'data',
        'related_id',
        'related_type',
        'is_read',
        'read_at',
        'is_important',
        'is_sent',
        'sent_at',
        'send_email',
        'send_sms',
        'send_push',
        'created_by',
    ];

    protected $casts = [
        'recipient_ids' => 'array',
        'data' => 'array',
        'is_read' => 'boolean',
        'is_important' => 'boolean',
        'is_sent' => 'boolean',
        'send_email' => 'boolean',
        'send_sms' => 'boolean',
        'send_push' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع منشئ الإشعار
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor لنوع الإشعار بالعربية
     */
    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'leave_request' => 'طلب إجازة',
            'leave_approved' => 'موافقة على إجازة',
            'leave_rejected' => 'رفض إجازة',
            'attendance' => 'حضور',
            'salary' => 'راتب',
            'performance_review' => 'تقييم أداء',
            'training' => 'تدريب',
            'recruitment' => 'توظيف',
            'benefit' => 'ميزة',
            'system' => 'نظام',
            'reminder' => 'تذكير',
            'contract_expiry_reminder' => 'تذكير انتهاء عقد',
            default => $this->type,
        };
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
