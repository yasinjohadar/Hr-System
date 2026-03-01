<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->integer('approval_level'); // مستوى الموافقة (1, 2, 3...)
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete(); // الموافق
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comments')->nullable(); // تعليقات الموافق
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->integer('sort_order')->default(0); // ترتيب الموافقة
            $table->timestamps();
            
            // منع تكرار الموافقة من نفس المستخدم
            $table->unique(['payroll_id', 'approval_level', 'approver_id'], 'unique_approval');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_approvals');
    }
};
