<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EmployeeDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'document_type',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'issue_date',
        'expiry_date',
        'is_expired',
        'is_required',
        'status',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_expired' => 'boolean',
        'is_required' => 'boolean',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع من رفع المستند
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Accessor لنوع المستند بالعربية
     */
    public function getDocumentTypeNameArAttribute(): string
    {
        return match($this->document_type) {
            'contract' => 'عقد عمل',
            'certificate' => 'شهادة',
            'visa' => 'تأشيرة',
            'id' => 'هوية',
            'passport' => 'جواز سفر',
            'license' => 'رخصة',
            'other' => 'أخرى',
            default => $this->document_type,
        };
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'expired' => 'منتهي',
            'pending' => 'قيد الانتظار',
            'rejected' => 'مرفوض',
            default => $this->status,
        };
    }

    /**
     * التحقق من انتهاء الصلاحية
     */
    public function checkExpiry()
    {
        if ($this->expiry_date && $this->expiry_date->isPast() && !$this->is_expired) {
            $this->update([
                'is_expired' => true,
                'status' => 'expired',
            ]);
        }
    }

    /**
     * Scope للمستندات المنتهية الصلاحية
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                    ->where('expiry_date', '>', Carbon::now())
                    ->where('is_expired', false);
    }
}
