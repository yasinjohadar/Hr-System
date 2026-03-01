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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->string('question_text');
            $table->string('question_text_ar')->nullable();
            $table->enum('question_type', ['text', 'textarea', 'radio', 'checkbox', 'rating', 'date', 'number'])->default('text');
            $table->json('options')->nullable(); // خيارات للأسئلة من نوع radio/checkbox
            $table->integer('question_order');
            $table->boolean('is_required')->default(true);
            $table->text('help_text')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
