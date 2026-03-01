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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code')->unique();
            $table->string('subject');
            $table->string('subject_ar')->nullable();
            $table->text('body'); // HTML content
            $table->text('body_ar')->nullable();
            $table->enum('type', ['welcome', 'leave_approved', 'leave_rejected', 'salary', 'birthday', 'anniversary', 'custom'])->default('custom');
            $table->json('variables')->nullable(); // متغيرات قابلة للاستخدام
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('email_templates');
    }
};
