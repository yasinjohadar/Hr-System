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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->integer('year'); // السنة
            $table->integer('total_days')->default(0); // إجمالي الأيام المخصصة
            $table->integer('used_days')->default(0); // الأيام المستخدمة
            $table->integer('remaining_days')->default(0); // الأيام المتبقية
            $table->integer('carried_forward')->default(0); // الأيام المحمولة من العام السابق
            $table->timestamps();
            $table->softDeletes();
            
            // منع تكرار الرصيد لنفس الموظف ونوع الإجازة في نفس السنة
            $table->unique(['employee_id', 'leave_type_id', 'year'], 'unique_employee_leave_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
