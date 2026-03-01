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
        Schema::create('feedback_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_request_id')->constrained('feedback_requests')->cascadeOnDelete();
            $table->foreignId('respondent_id')->nullable()->constrained('employees')->nullOnDelete(); // من قام بالتقييم
            $table->enum('relationship_type', ['manager', 'peer', 'subordinate', 'self', 'other'])->default('peer');
            $table->json('ratings')->nullable(); // التقييمات (JSON)
            $table->text('strengths')->nullable(); // نقاط القوة
            $table->text('weaknesses')->nullable(); // نقاط الضعف
            $table->text('recommendations')->nullable(); // التوصيات
            $table->text('comments')->nullable(); // تعليقات عامة
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'draft'])->default('pending');
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['feedback_request_id', 'respondent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_responses');
    }
};
