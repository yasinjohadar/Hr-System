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
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('benefit_type_id')->constrained('benefit_types')->cascadeOnDelete();
            
            // القيمة
            $table->decimal('value', 10, 2)->nullable(); // قيمة الميزة
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            
            // التواريخ
            $table->date('start_date'); // تاريخ البدء
            $table->date('end_date')->nullable(); // تاريخ الانتهاء (null = دائم)
            
            // الحالة
            $table->enum('status', ['active', 'suspended', 'expired', 'cancelled'])->default('active');
            
            // معلومات إضافية
            $table->text('notes')->nullable(); // ملاحظات
            $table->text('approval_notes')->nullable(); // ملاحظات الموافقة
            $table->date('approval_date')->nullable(); // تاريخ الموافقة
            
            // الملفات المرفقة
            $table->string('document_path')->nullable(); // مسار المستند المرفق
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // منع تكرار الميزة نفسها للموظف نفسه في نفس الفترة
            $table->unique(['employee_id', 'benefit_type_id', 'start_date'], 'unique_employee_benefit');
            
            // Indexes
            $table->index('employee_id');
            $table->index('benefit_type_id');
            $table->index('status');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_benefits');
    }
};
