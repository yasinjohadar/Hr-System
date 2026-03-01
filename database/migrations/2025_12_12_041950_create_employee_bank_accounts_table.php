<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('bank_name'); // اسم البنك
            $table->string('bank_name_ar')->nullable();
            $table->string('account_number'); // رقم الحساب
            $table->string('iban')->nullable(); // IBAN
            $table->string('swift_code')->nullable(); // SWIFT Code
            $table->string('account_holder_name')->nullable(); // اسم صاحب الحساب
            $table->string('branch_name')->nullable(); // اسم الفرع
            $table->string('branch_address')->nullable(); // عنوان الفرع
            $table->enum('account_type', ['savings', 'current', 'salary'])->default('salary');
            $table->string('currency_code', 3)->default('SAR'); // عملة الحساب
            $table->boolean('is_primary')->default(false); // الحساب الأساسي
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // منع تكرار الحساب الأساسي للموظف
            $table->unique(['employee_id', 'is_primary'], 'unique_primary_account');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_bank_accounts');
    }
};
