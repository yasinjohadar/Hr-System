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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_vacancy_id')->constrained('job_vacancies')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            
            // معلومات التقديم
            $table->date('application_date'); // تاريخ التقديم
            $table->enum('source', ['website', 'linkedin', 'referral', 'indeed', 'other'])->default('website'); // مصدر التقديم
            $table->string('referrer_name')->nullable(); // اسم المُحيل (إذا كان referral)
            
            // الحالة
            $table->enum('status', ['pending', 'reviewing', 'shortlisted', 'interviewed', 'offered', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->text('rejection_reason')->nullable(); // سبب الرفض
            $table->date('rejection_date')->nullable(); // تاريخ الرفض
            
            // التقييم
            $table->integer('rating')->nullable(); // التقييم (1-5)
            $table->text('reviewer_notes')->nullable(); // ملاحظات المراجع
            
            // الملفات المرفقة
            $table->string('cv_path')->nullable(); // مسار السيرة الذاتية
            $table->string('cover_letter_path')->nullable(); // مسار خطاب التقديم
            $table->json('additional_documents')->nullable(); // مستندات إضافية
            
            // معلومات إضافية
            $table->text('notes')->nullable(); // ملاحظات
            $table->decimal('expected_salary', 10, 2)->nullable(); // الراتب المتوقع
            $table->date('available_start_date')->nullable(); // تاريخ البدء المتاح
            
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // منع التقديم المتكرر لنفس المرشح لنفس الوظيفة
            $table->unique(['job_vacancy_id', 'candidate_id'], 'unique_job_candidate');
            
            // Indexes
            $table->index('status');
            $table->index('application_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
