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
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود المكون (مثل: HOUSING_ALLOWANCE, TAX_DEDUCTION)
            $table->string('name'); // اسم المكون
            $table->string('name_ar')->nullable(); // اسم المكون بالعربية
            $table->enum('type', ['allowance', 'deduction', 'bonus', 'overtime']); // نوع المكون
            
            // طريقة الحساب
            $table->enum('calculation_type', ['fixed', 'percentage', 'formula', 'attendance_based', 'leave_based'])->default('fixed');
            $table->decimal('default_value', 12, 2)->default(0); // القيمة الافتراضية
            $table->decimal('percentage', 5, 2)->nullable(); // النسبة المئوية (من الراتب الأساسي)
            $table->string('formula')->nullable(); // الصيغة الحسابية
            
            // القواعد
            $table->decimal('min_value', 12, 2)->nullable(); // الحد الأدنى
            $table->decimal('max_value', 12, 2)->nullable(); // الحد الأقصى
            $table->boolean('is_taxable')->default(false); // خاضع للضريبة
            $table->boolean('is_required')->default(false); // إلزامي
            
            // التطبيق
            $table->boolean('apply_to_all')->default(false); // يطبق على جميع الموظفين
            $table->json('applicable_positions')->nullable(); // المناصب المناسبة (JSON array)
            $table->json('applicable_departments')->nullable(); // الأقسام المناسبة (JSON array)
            
            // الحالة
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // فهارس
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
