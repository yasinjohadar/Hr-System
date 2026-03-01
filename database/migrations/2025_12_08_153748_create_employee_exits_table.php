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
        Schema::create('employee_exits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('resignation_date'); // تاريخ الاستقالة
            $table->date('last_working_day'); // آخر يوم عمل
            $table->enum('exit_type', ['resignation', 'termination', 'retirement', 'end_of_contract', 'other'])->default('resignation');
            $table->text('reason')->nullable(); // سبب المغادرة
            $table->text('reason_ar')->nullable(); // السبب بالعربية
            $table->enum('status', ['pending', 'in_process', 'completed', 'cancelled'])->default('pending');
            
            // استبيان إنهاء الخدمة
            $table->integer('exit_interview_rating')->nullable(); // تقييم المقابلة (1-5)
            $table->text('exit_interview_feedback')->nullable(); // ملاحظات المقابلة
            $table->text('suggestions')->nullable(); // اقتراحات
            $table->boolean('exit_interview_completed')->default(false);
            
            // استرجاع الأصول
            $table->boolean('assets_returned')->default(false);
            $table->text('assets_notes')->nullable();
            
            // تسليم المهام
            $table->boolean('handover_completed')->default(false);
            $table->text('handover_notes')->nullable();
            $table->foreignId('handover_to')->nullable()->constrained('employees')->nullOnDelete();
            
            // المستندات
            $table->boolean('documents_returned')->default(false);
            $table->text('documents_notes')->nullable();
            
            // الحسابات
            $table->boolean('final_settlement_completed')->default(false);
            $table->decimal('final_settlement_amount', 10, 2)->nullable();
            $table->date('final_settlement_date')->nullable();
            
            // الموافقات
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('employee_id');
            $table->index('status');
            $table->index('exit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_exits');
    }
};
