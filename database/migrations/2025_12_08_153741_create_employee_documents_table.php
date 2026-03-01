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
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('document_type'); // نوع المستند (contract, certificate, visa, id, etc.)
            $table->string('title'); // عنوان المستند
            $table->text('description')->nullable(); // وصف المستند
            $table->string('file_path'); // مسار الملف
            $table->string('file_name'); // اسم الملف الأصلي
            $table->string('file_size')->nullable(); // حجم الملف
            $table->string('mime_type')->nullable(); // نوع الملف
            $table->date('issue_date')->nullable(); // تاريخ الإصدار
            $table->date('expiry_date')->nullable(); // تاريخ انتهاء الصلاحية
            $table->boolean('is_expired')->default(false); // منتهي الصلاحية
            $table->boolean('is_required')->default(false); // مطلوب
            $table->enum('status', ['active', 'expired', 'pending', 'rejected'])->default('active');
            $table->text('notes')->nullable(); // ملاحظات
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('employee_id');
            $table->index('document_type');
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
