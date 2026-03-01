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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم التقرير
            $table->string('name_ar')->nullable(); // اسم التقرير بالعربية
            $table->string('report_type'); // نوع التقرير (employees, attendance, salaries, leaves, etc.)
            $table->text('description')->nullable(); // وصف التقرير
            
            // معايير التقرير (JSON)
            $table->json('criteria')->nullable(); // المعايير المستخدمة في التقرير
            
            // معلومات التقرير
            $table->enum('format', ['view', 'pdf', 'excel', 'csv'])->default('view'); // تنسيق التقرير
            $table->string('file_path')->nullable(); // مسار الملف المحفوظ (للتقارير المصدرة)
            
            // إحصائيات التقرير
            $table->integer('total_records')->default(0); // إجمالي السجلات
            $table->json('summary')->nullable(); // ملخص التقرير (JSON)
            
            // الحالة
            $table->enum('status', ['draft', 'generated', 'archived'])->default('draft');
            $table->date('generated_date')->nullable(); // تاريخ التوليد
            $table->date('period_start')->nullable(); // بداية الفترة
            $table->date('period_end')->nullable(); // نهاية الفترة
            
            // معلومات إضافية
            $table->text('notes')->nullable(); // ملاحظات
            $table->boolean('is_public')->default(false); // تقرير عام
            $table->boolean('is_scheduled')->default(false); // تقرير مجدول
            $table->string('schedule_frequency')->nullable(); // تكرار الجدولة (daily, weekly, monthly)
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('report_type');
            $table->index('status');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
