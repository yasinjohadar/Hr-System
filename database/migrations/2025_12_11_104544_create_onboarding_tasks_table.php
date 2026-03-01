<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('onboarding_templates')->nullOnDelete();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->enum('task_type', ['document', 'training', 'meeting', 'system_access', 'other'])->default('other');
            $table->integer('task_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->integer('estimated_duration_minutes')->nullable();
            $table->text('instructions')->nullable();
            $table->string('assigned_to_role')->nullable(); // role name
            $table->foreignId('assigned_to_employee')->nullable()->constrained('employees')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_tasks');
    }
};
