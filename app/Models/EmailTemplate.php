<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'subject',
        'subject_ar',
        'body',
        'body_ar',
        'type',
        'variables',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'welcome' => 'ترحيب',
            'leave_approved' => 'موافقة إجازة',
            'leave_rejected' => 'رفض إجازة',
            'salary' => 'راتب',
            'birthday' => 'عيد ميلاد',
            'anniversary' => 'ذكرى سنوية',
            'custom' => 'مخصص',
            default => $this->type,
        };
    }
}
