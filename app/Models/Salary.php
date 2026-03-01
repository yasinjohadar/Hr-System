<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'salary_month',
        'salary_year',
        'base_salary',
        'allowances',
        'bonuses',
        'deductions',
        'overtime',
        'total_salary',
        'currency_id',
        'payment_date',
        'payment_status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'deductions' => 'decimal:2',
        'overtime' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع العملة
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * حساب الراتب الإجمالي تلقائياً
     */
    public function calculateTotal(): void
    {
        $this->total_salary = $this->base_salary 
            + $this->allowances 
            + $this->bonuses 
            + $this->overtime 
            - $this->deductions;
    }

    /**
     * الحصول على اسم الشهر
     */
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        return $months[$this->salary_month] ?? '';
    }

    /**
     * الحصول على حالة الدفع بالعربية
     */
    public function getPaymentStatusArAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'قيد الانتظار',
            'paid' => 'مدفوع',
            'cancelled' => 'ملغي',
            default => $this->payment_status
        };
    }
}
