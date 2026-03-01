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
        Schema::create('expense_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique(); // رقم الطلب
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // المبلغ
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->date('expense_date'); // تاريخ المصروف
            $table->text('description'); // وصف المصروف
            $table->text('description_ar')->nullable();
            $table->string('receipt_path')->nullable(); // مسار الإيصال/الفواتير
            $table->string('receipt_file_name')->nullable();
            $table->string('receipt_file_size')->nullable();
            $table->string('payment_method')->nullable(); // طريقة الدفع (نقد، بطاقة، تحويل)
            $table->string('vendor_name')->nullable(); // اسم المورد/المؤسسة
            $table->string('project_code')->nullable(); // كود المشروع (إن وجد)
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable(); // سبب الرفض
            $table->date('paid_date')->nullable(); // تاريخ الدفع
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete(); // من قام بالدفع
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('expense_requests');
    }
};
