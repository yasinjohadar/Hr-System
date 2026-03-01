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
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->string('item_type'); // allowance, deduction, bonus, overtime
            $table->string('item_name'); // اسم البند
            $table->string('item_name_ar')->nullable(); // اسم البند بالعربية
            $table->string('component_code')->nullable(); // كود المكون (للربط مع salary_components)
            
            // القيمة
            $table->enum('calculation_type', ['fixed', 'percentage', 'formula'])->default('fixed');
            $table->decimal('amount', 12, 2)->default(0); // القيمة
            $table->decimal('percentage', 5, 2)->nullable(); // النسبة المئوية (إذا كان calculation_type = percentage)
            $table->string('formula')->nullable(); // الصيغة (إذا كان calculation_type = formula)
            
            // تفاصيل إضافية
            $table->integer('quantity')->default(1); // الكمية (مثل عدد أيام السفر)
            $table->decimal('unit_price', 10, 2)->nullable(); // سعر الوحدة
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // بيانات إضافية (JSON)
            
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->timestamps();
            
            // فهارس
            $table->index('payroll_id');
            $table->index('item_type');
            $table->index('component_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
