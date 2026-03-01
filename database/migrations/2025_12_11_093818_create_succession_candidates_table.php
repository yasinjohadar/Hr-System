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
        Schema::create('succession_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('succession_plan_id')->constrained('succession_plans')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('readiness_level', ['ready_now', 'ready_1_year', 'ready_2_years', 'ready_3_years', 'not_ready'])->default('ready_1_year');
            $table->integer('readiness_score')->default(0); // درجة الاستعداد (0-100)
            $table->text('strengths')->nullable(); // نقاط القوة
            $table->text('development_needs')->nullable(); // احتياجات التطوير
            $table->text('action_plan')->nullable(); // خطة العمل
            $table->enum('status', ['potential', 'identified', 'developing', 'ready', 'selected', 'rejected'])->default('potential');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['succession_plan_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('succession_candidates');
    }
};
