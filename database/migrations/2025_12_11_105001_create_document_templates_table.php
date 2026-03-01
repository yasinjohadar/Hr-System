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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['contract', 'letter', 'certificate', 'report', 'custom'])->default('custom');
            $table->text('content'); // محتوى القالب (HTML/Markdown)
            $table->text('content_ar')->nullable();
            $table->json('variables')->nullable(); // متغيرات قابلة للاستخدام
            $table->string('file_format')->default('pdf'); // pdf, docx, html
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
        Schema::dropIfExists('document_templates');
    }
};
