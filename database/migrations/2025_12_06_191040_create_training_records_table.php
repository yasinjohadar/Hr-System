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
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('status', ['registered', 'attending', 'completed', 'failed', 'cancelled'])->default('registered'); // حالة التسجيل
            $table->date('registration_date')->nullable(); // تاريخ التسجيل
            $table->date('completion_date')->nullable(); // تاريخ الإتمام
            $table->decimal('score', 5, 2)->nullable(); // الدرجة/النتيجة
            $table->text('feedback')->nullable(); // ملاحظات الموظف
            $table->text('evaluation')->nullable(); // تقييم المدرب
            $table->boolean('certificate_issued')->default(false); // هل تم إصدار شهادة
            $table->date('certificate_date')->nullable(); // تاريخ إصدار الشهادة
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // منع التسجيل المكرر لنفس الموظف في نفس الدورة
            $table->unique(['training_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_records');
    }
};
