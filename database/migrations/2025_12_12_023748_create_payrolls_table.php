<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_code')->unique(); // كود كشف الراتب
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->integer('payroll_month'); // 1-12
            $table->integer('payroll_year'); // 2024, 2025, etc.
            $table->date('period_start'); // بداية الفترة
            $table->date('period_end'); // نهاية الفترة
            
            // الراتب الأساسي
            $table->decimal('base_salary', 12, 2)->default(0);
            
            // البدلات (إجمالي)
            $table->decimal('total_allowances', 12, 2)->default(0);
            
            // الخصومات (إجمالي)
            $table->decimal('total_deductions', 12, 2)->default(0);
            
            // المكافآت
            $table->decimal('bonuses', 12, 2)->default(0);
            
            // الساعات الإضافية
            $table->decimal('overtime_amount', 12, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            
            // الإجازات
            $table->integer('leave_days')->default(0); // أيام الإجازة
            $table->decimal('leave_deduction', 12, 2)->default(0); // خصم الإجازات
            
            // الحضور
            $table->integer('working_days')->default(0); // أيام العمل
            $table->integer('present_days')->default(0); // أيام الحضور
            $table->integer('absent_days')->default(0); // أيام الغياب
            $table->integer('late_days')->default(0); // أيام التأخير
            $table->decimal('late_deduction', 12, 2)->default(0); // خصم التأخير
            
            // الإجمالي
            $table->decimal('gross_salary', 12, 2)->default(0); // الراتب الإجمالي قبل الخصومات
            $table->decimal('net_salary', 12, 2)->default(0); // الراتب الصافي
            
            // العملة
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            
            // حالة الدفع
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'other'])->nullable();
            $table->string('payment_reference')->nullable(); // رقم المرجع
            
            // معلومات إضافية
            $table->text('notes')->nullable();
            $table->json('calculation_details')->nullable(); // تفاصيل الحساب (JSON)
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // منع تكرار كشف الراتب لنفس الموظف في نفس الشهر والسنة
            $table->unique(['employee_id', 'payroll_month', 'payroll_year'], 'unique_employee_payroll');
            
            // فهارس للبحث
            $table->index(['payroll_month', 'payroll_year']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
