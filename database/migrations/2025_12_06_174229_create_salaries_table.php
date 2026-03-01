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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->integer('salary_month'); // 1-12
            $table->integer('salary_year'); // 2024, 2025, etc.
            $table->decimal('base_salary', 10, 2); // الراتب الأساسي
            $table->decimal('allowances', 10, 2)->default(0); // البدلات (سكن، مواصلات، إلخ)
            $table->decimal('bonuses', 10, 2)->default(0); // المكافآت
            $table->decimal('deductions', 10, 2)->default(0); // الخصومات
            $table->decimal('overtime', 10, 2)->default(0); // ساعات إضافية
            $table->decimal('total_salary', 10, 2); // الراتب الإجمالي
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->date('payment_date')->nullable(); // تاريخ الدفع
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->text('notes')->nullable(); // ملاحظات
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // منع تكرار الراتب لنفس الموظف في نفس الشهر والسنة
            $table->unique(['employee_id', 'salary_month', 'salary_year'], 'unique_employee_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
