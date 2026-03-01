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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('attendance_date'); // تاريخ الحضور
            $table->time('check_in')->nullable(); // وقت الدخول
            $table->time('check_out')->nullable(); // وقت الخروج
            $table->time('expected_check_in')->nullable(); // وقت الدخول المتوقع
            $table->time('expected_check_out')->nullable(); // وقت الخروج المتوقع
            $table->integer('hours_worked')->default(0); // ساعات العمل (بالدقائق)
            $table->integer('overtime_minutes')->default(0); // ساعات إضافية (بالدقائق)
            $table->integer('late_minutes')->default(0); // دقائق التأخير
            $table->integer('early_leave_minutes')->default(0); // دقائق الانصراف المبكر
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave', 'holiday'])->default('absent');
            $table->text('notes')->nullable(); // ملاحظات
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // منع تكرار الحضور لنفس الموظف في نفس اليوم
            $table->unique(['employee_id', 'attendance_date'], 'unique_employee_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
