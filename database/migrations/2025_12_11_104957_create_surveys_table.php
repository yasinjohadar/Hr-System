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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('survey_code')->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['satisfaction', 'climate', 'engagement', 'exit', 'custom'])->default('satisfaction');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'active', 'closed', 'cancelled'])->default('draft');
            $table->boolean('is_anonymous')->default(true);
            $table->enum('target_audience', ['all', 'department', 'branch', 'position', 'custom'])->default('all');
            $table->json('target_ids')->nullable(); // IDs للجمهور المستهدف
            $table->integer('total_responses')->default(0);
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
        Schema::dropIfExists('surveys');
    }
};
