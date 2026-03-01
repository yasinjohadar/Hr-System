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
        Schema::create('overtime_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->date('overtime_date'); // تاريخ الساعات الإضافية
            $table->time('start_time'); // وقت البدء
            $table->time('end_time'); // وقت الانتهاء
            $table->integer('overtime_minutes'); // الساعات الإضافية بالدقائق
            $table->decimal('overtime_hours', 5, 2); // الساعات الإضافية بالساعات
            
            // نوع الساعات الإضافية
            $table->enum('overtime_type', ['regular', 'holiday', 'night', 'weekend'])->default('regular');
            $table->decimal('rate_multiplier', 3, 2)->default(1.5); // معدل الضرب (1.5 = 150%)
            
            // الحساب
            $table->decimal('hourly_rate', 10, 2)->nullable(); // معدل الساعة
            $table->decimal('overtime_amount', 12, 2)->default(0); // مبلغ الساعات الإضافية
            
            // الموافقة
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            // الربط بالراتب
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            
            // معلومات إضافية
            $table->text('reason')->nullable(); // سبب الساعات الإضافية
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // فهارس
            $table->index(['employee_id', 'overtime_date']);
            $table->index('status');
            $table->index('payroll_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_records');
    }
};
