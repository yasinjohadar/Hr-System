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
        Schema::create('employee_job_changes', function (Blueprint $table) {
            $table->id();

            // الهوية والربط
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('change_type', ['transfer', 'promotion', 'salary_change', 'demotion']);

            // الحالة والموافقة
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('effective_date');
            $table->text('reason')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // لقطة القيم قبل وبعد
            // القسم
            $table->foreignId('old_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('new_department_id')->nullable()->constrained('departments')->nullOnDelete();

            // المنصب
            $table->foreignId('old_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('new_position_id')->nullable()->constrained('positions')->nullOnDelete();

            // الفرع
            $table->foreignId('old_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('new_branch_id')->nullable()->constrained('branches')->nullOnDelete();

            // المدير
            $table->foreignId('old_manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('new_manager_id')->nullable()->constrained('employees')->nullOnDelete();

            // الراتب
            $table->decimal('old_salary', 10, 2)->nullable();
            $table->decimal('new_salary', 10, 2)->nullable();

            // زمنية
            $table->timestamps();

            // فهارس
            $table->index('employee_id');
            $table->index('status');
            $table->index('effective_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_job_changes');
    }
};
