<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_code',
        'employee_id',
        'expense_category_id',
        'amount',
        'currency_id',
        'expense_date',
        'description',
        'description_ar',
        'receipt_path',
        'receipt_file_name',
        'receipt_file_size',
        'payment_method',
        'vendor_name',
        'project_code',
        'status',
        'rejection_reason',
        'paid_date',
        'paid_by',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'paid_date' => 'date',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع تصنيف المصروف
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    /**
     * العلاقة مع العملة
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * العلاقة مع الموافقات
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(ExpenseApproval::class);
    }

    /**
     * العلاقة مع من قام بالدفع
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * العلاقة مع منشئ الطلب
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
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'paid' => 'مدفوع',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    /**
     * Accessor لطريقة الدفع بالعربية
     */
    public function getPaymentMethodNameArAttribute(): ?string
    {
        if (!$this->payment_method) {
            return null;
        }

        return match($this->payment_method) {
            'cash' => 'نقد',
            'card' => 'بطاقة',
            'transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            default => $this->payment_method,
        };
    }

    /**
     * التحقق من إمكانية الموافقة
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * التحقق من إمكانية الدفع
     */
    public function canBePaid(): bool
    {
        return $this->status === 'approved';
    }
}
