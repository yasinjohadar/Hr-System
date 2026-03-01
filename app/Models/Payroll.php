<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Payroll extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payroll_code',
        'employee_id',
        'payroll_month',
        'payroll_year',
        'period_start',
        'period_end',
        'base_salary',
        'total_allowances',
        'total_deductions',
        'bonuses',
        'overtime_amount',
        'overtime_hours',
        'leave_days',
        'leave_deduction',
        'working_days',
        'present_days',
        'absent_days',
        'late_days',
        'late_deduction',
        'gross_salary',
        'income_tax',
        'social_insurance_employee',
        'social_insurance_employer',
        'health_insurance_employee',
        'health_insurance_employer',
        'other_taxes',
        'total_taxes',
        'net_salary',
        'total_employer_cost',
        'currency_id',
        'status',
        'payment_date',
        'payment_method',
        'payment_reference',
        'notes',
        'calculation_details',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'base_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'leave_deduction' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'income_tax' => 'decimal:2',
        'social_insurance_employee' => 'decimal:2',
        'social_insurance_employer' => 'decimal:2',
        'health_insurance_employee' => 'decimal:2',
        'health_insurance_employer' => 'decimal:2',
        'other_taxes' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'total_employer_cost' => 'decimal:2',
        'calculation_details' => 'array',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($payroll) {
            if (empty($payroll->payroll_code)) {
                $payroll->payroll_code = 'PR-' . strtoupper(Str::random(8));
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class)->orderBy('sort_order');
    }

    public function allowances(): HasMany
    {
        return $this->items()->where('item_type', 'allowance');
    }

    public function deductions(): HasMany
    {
        return $this->items()->where('item_type', 'deduction');
    }

    public function overtimeRecords(): HasMany
    {
        return $this->hasMany(OvertimeRecord::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PayrollPayment::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PayrollApproval::class)->orderBy('approval_level');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        return $months[$this->payroll_month] ?? '';
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'calculated' => 'محسوب',
            'approved' => 'موافق عليه',
            'paid' => 'مدفوع',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function getPaymentMethodNameArAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'cheque' => 'شيك',
            'other' => 'أخرى',
            default => $this->payment_method ?? '-',
        };
    }

    /**
     * حساب الراتب الإجمالي والصافي
     */
    public function calculateTotals(): void
    {
        // حساب الإجمالي قبل الخصومات
        $this->gross_salary = $this->base_salary 
            + $this->total_allowances 
            + $this->bonuses 
            + $this->overtime_amount;

        // حساب إجمالي الضرائب
        $this->total_taxes = $this->income_tax 
            + $this->social_insurance_employee 
            + $this->health_insurance_employee 
            + $this->other_taxes;

        // حساب الصافي بعد الخصومات والضرائب
        $this->net_salary = $this->gross_salary 
            - $this->total_deductions 
            - $this->leave_deduction 
            - $this->late_deduction
            - $this->total_taxes;

        // حساب إجمالي تكلفة صاحب العمل
        $this->total_employer_cost = $this->gross_salary 
            + $this->social_insurance_employer 
            + $this->health_insurance_employer;
    }
}
