<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->string('payment_code')->unique(); // كود الدفعة
            $table->decimal('amount', 12, 2); // المبلغ المدفوع
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'card', 'other'])->default('bank_transfer');
            $table->date('payment_date'); // تاريخ الدفع
            $table->string('reference_number')->nullable(); // رقم المرجع (رقم الشيك، رقم التحويل)
            $table->foreignId('bank_account_id')->nullable()->constrained('employee_bank_accounts')->nullOnDelete();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('payment_notes')->nullable();
            $table->text('failure_reason')->nullable(); // سبب الفشل
            $table->timestamp('processed_at')->nullable(); // وقت المعالجة
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_payments');
    }
};
