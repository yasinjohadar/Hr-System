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
        Schema::create('employee_violations', function (Blueprint $table) {
            $table->id();
            $table->string('violation_code')->unique(); // رقم المخالفة
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('violation_type_id')->constrained('violation_types')->cascadeOnDelete();
            $table->foreignId('disciplinary_action_id')->nullable()->constrained('disciplinary_actions')->nullOnDelete();
            $table->date('violation_date'); // تاريخ المخالفة
            $table->text('description'); // وصف المخالفة
            $table->text('description_ar')->nullable();
            $table->text('witnesses')->nullable(); // الشهود
            $table->text('employee_response')->nullable(); // رد الموظف
            $table->enum('status', ['pending', 'investigating', 'confirmed', 'dismissed', 'resolved'])->default('pending');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete(); // من أبلغ عن المخالفة
            $table->foreignId('investigated_by')->nullable()->constrained('users')->nullOnDelete(); // من قام بالتحقيق
            $table->date('investigation_date')->nullable(); // تاريخ التحقيق
            $table->text('investigation_notes')->nullable(); // ملاحظات التحقيق
            $table->date('action_date')->nullable(); // تاريخ تطبيق الإجراء
            $table->text('action_notes')->nullable(); // ملاحظات الإجراء
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // من وافق على الإجراء
            $table->date('approval_date')->nullable(); // تاريخ الموافقة
            $table->text('resolution_notes')->nullable(); // ملاحظات الحل
            $table->date('resolution_date')->nullable(); // تاريخ الحل
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete(); // ربط بالحضور (إن وجد)
            $table->foreignId('leave_request_id')->nullable()->constrained('leave_requests')->nullOnDelete(); // ربط بالإجازة (إن وجد)
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_violations');
    }
};
