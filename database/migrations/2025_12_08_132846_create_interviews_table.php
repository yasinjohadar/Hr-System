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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('job_vacancy_id')->constrained('job_vacancies')->cascadeOnDelete();
            
            // معلومات المقابلة
            $table->string('title')->nullable(); // عنوان المقابلة
            $table->enum('type', ['phone', 'video', 'in_person', 'panel', 'technical', 'hr', 'final'])->default('in_person');
            $table->enum('round', ['first', 'second', 'third', 'final'])->default('first'); // جولة المقابلة
            
            // التواريخ والأوقات
            $table->date('interview_date'); // تاريخ المقابلة
            $table->time('interview_time'); // وقت المقابلة
            $table->time('duration')->nullable(); // مدة المقابلة (بالدقائق)
            $table->string('timezone')->default('UTC'); // المنطقة الزمنية
            
            // الموقع
            $table->string('location')->nullable(); // مكان المقابلة
            $table->string('meeting_link')->nullable(); // رابط الاجتماع (للمقابلات عن بُعد)
            $table->text('address')->nullable(); // العنوان الكامل
            
            // المقابِلون
            $table->json('interviewers')->nullable(); // قائمة المقابِلين (JSON array of employee IDs)
            $table->foreignId('scheduled_by')->nullable()->constrained('users')->nullOnDelete();
            
            // الحالة
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rescheduled', 'no_show'])->default('scheduled');
            $table->text('cancellation_reason')->nullable(); // سبب الإلغاء
            
            // نتائج المقابلة
            $table->integer('overall_rating')->nullable(); // التقييم الإجمالي (1-5)
            $table->text('technical_skills_rating')->nullable(); // تقييم المهارات التقنية
            $table->text('communication_rating')->nullable(); // تقييم التواصل
            $table->text('cultural_fit_rating')->nullable(); // تقييم الانسجام الثقافي
            $table->text('strengths')->nullable(); // نقاط القوة
            $table->text('weaknesses')->nullable(); // نقاط الضعف
            $table->text('recommendation')->nullable(); // التوصية
            $table->enum('recommendation_status', ['hire', 'maybe', 'reject', 'pending'])->nullable();
            $table->text('interview_notes')->nullable(); // ملاحظات المقابلة
            $table->text('candidate_feedback')->nullable(); // ملاحظات المرشح
            
            // معلومات إضافية
            $table->text('questions_asked')->nullable(); // الأسئلة المطروحة
            $table->text('answers_given')->nullable(); // الإجابات
            $table->text('next_steps')->nullable(); // الخطوات التالية
            
            $table->foreignId('conducted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('interview_date');
            $table->index('status');
            $table->index('job_application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
