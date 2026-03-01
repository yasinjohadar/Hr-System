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
        Schema::create('feedback_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete(); // الموظف الذي يتم تقييمه
            $table->enum('feedback_type', ['360_degree', 'peer', 'subordinate', 'self', 'custom'])->default('360_degree');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'active', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->text('instructions')->nullable(); // تعليمات التقييم
            $table->boolean('is_anonymous')->default(false); // هل التقييم مجهول
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
        Schema::dropIfExists('feedback_requests');
    }
};
