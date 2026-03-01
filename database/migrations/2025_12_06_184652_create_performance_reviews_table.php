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
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('employees')->cascadeOnDelete(); // المقيّم (المدير)
            $table->string('review_period'); // فترة التقييم (مثل: Q1 2024, Annual 2024)
            $table->date('review_date'); // تاريخ التقييم
            $table->date('period_start_date'); // تاريخ بداية الفترة
            $table->date('period_end_date'); // تاريخ نهاية الفترة
            
            // التقييمات في مجالات مختلفة (من 1 إلى 5)
            $table->integer('job_knowledge')->default(0); // المعرفة الوظيفية
            $table->integer('work_quality')->default(0); // جودة العمل
            $table->integer('productivity')->default(0); // الإنتاجية
            $table->integer('communication')->default(0); // التواصل
            $table->integer('teamwork')->default(0); // العمل الجماعي
            $table->integer('initiative')->default(0); // المبادرة
            $table->integer('problem_solving')->default(0); // حل المشاكل
            $table->integer('attendance_punctuality')->default(0); // الحضور والانضباط
            
            $table->decimal('overall_rating', 3, 2)->default(0); // التقييم الإجمالي (متوسط)
            $table->text('strengths')->nullable(); // نقاط القوة
            $table->text('weaknesses')->nullable(); // نقاط الضعف
            $table->text('goals_achieved')->nullable(); // الأهداف المحققة
            $table->text('future_goals')->nullable(); // الأهداف المستقبلية
            $table->text('comments')->nullable(); // تعليقات المقيّم
            $table->text('employee_comments')->nullable(); // تعليقات الموظف
            $table->enum('status', ['draft', 'completed', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
