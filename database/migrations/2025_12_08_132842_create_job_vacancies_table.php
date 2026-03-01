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
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الوظيفة
            $table->string('title_ar')->nullable(); // عنوان الوظيفة بالعربية
            $table->string('code')->unique(); // كود الوظيفة
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            
            // وصف الوظيفة
            $table->text('description')->nullable(); // الوصف بالإنجليزية
            $table->text('description_ar')->nullable(); // الوصف بالعربية
            $table->text('requirements')->nullable(); // المتطلبات
            $table->text('responsibilities')->nullable(); // المسؤوليات
            
            // معلومات الوظيفة
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern', 'freelance'])->default('full_time');
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'lead', 'executive'])->default('mid');
            $table->integer('years_of_experience')->nullable(); // سنوات الخبرة المطلوبة
            $table->string('education_level')->nullable(); // المستوى التعليمي المطلوب
            
            // الراتب
            $table->decimal('min_salary', 10, 2)->nullable(); // الحد الأدنى للراتب
            $table->decimal('max_salary', 10, 2)->nullable(); // الحد الأقصى للراتب
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            
            // التواريخ
            $table->date('posted_date'); // تاريخ النشر
            $table->date('closing_date')->nullable(); // تاريخ الإغلاق
            $table->date('start_date')->nullable(); // تاريخ البدء المتوقع
            
            // الحالة
            $table->enum('status', ['draft', 'published', 'closed', 'filled', 'cancelled'])->default('draft');
            $table->integer('number_of_positions')->default(1); // عدد المناصب المتاحة
            $table->integer('applications_count')->default(0); // عدد المتقدمين
            
            // معلومات إضافية
            $table->string('location')->nullable(); // الموقع
            $table->boolean('is_remote')->default(false); // عمل عن بُعد
            $table->text('benefits')->nullable(); // المزايا
            $table->text('notes')->nullable(); // ملاحظات
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('hiring_manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
            $table->index('status');
            $table->index('department_id');
            $table->index('position_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
