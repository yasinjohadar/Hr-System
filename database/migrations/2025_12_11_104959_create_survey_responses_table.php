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
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete(); // null إذا كان مجهول
            $table->json('answers'); // JSON للإجابات
            $table->text('comments')->nullable();
            $table->dateTime('submitted_at');
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['survey_id', 'employee_id']); // موظف واحد لكل استبيان (إذا لم يكن مجهول)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
