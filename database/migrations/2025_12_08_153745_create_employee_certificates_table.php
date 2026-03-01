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
        Schema::create('employee_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('certificate_name'); // اسم الشهادة
            $table->string('certificate_name_ar')->nullable(); // اسم الشهادة بالعربية
            $table->string('issuing_organization'); // الجهة المانحة
            $table->string('certificate_number')->nullable(); // رقم الشهادة
            $table->date('issue_date'); // تاريخ الإصدار
            $table->date('expiry_date')->nullable(); // تاريخ انتهاء الصلاحية
            $table->boolean('does_not_expire')->default(false); // لا تنتهي صلاحيتها
            $table->string('file_path')->nullable(); // مسار ملف الشهادة
            $table->enum('status', ['active', 'expired', 'pending', 'rejected'])->default('active');
            $table->text('description')->nullable(); // وصف الشهادة
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('employee_id');
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_certificates');
    }
};
