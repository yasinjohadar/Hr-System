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
        Schema::create('employee_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('performance_review_id')->nullable()->constrained('performance_reviews')->nullOnDelete();
            $table->string('title'); // عنوان الهدف
            $table->text('description')->nullable(); // وصف الهدف
            $table->enum('type', ['personal', 'team', 'department', 'company'])->default('personal');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->date('start_date'); // تاريخ البدء
            $table->date('target_date'); // تاريخ الهدف
            $table->date('completion_date')->nullable(); // تاريخ الإتمام
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'cancelled', 'on_hold'])->default('not_started');
            $table->integer('progress_percentage')->default(0); // نسبة التقدم (0-100)
            $table->text('success_criteria')->nullable(); // معايير النجاح
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('employee_id');
            $table->index('status');
            $table->index('target_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_goals');
    }
};
