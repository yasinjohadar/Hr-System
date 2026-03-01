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
        Schema::create('employee_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('reward_code')->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('reward_type_id')->constrained('reward_types')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('reward_date');
            $table->enum('reason', ['performance', 'achievement', 'milestone', 'recognition', 'other'])->default('recognition');
            $table->decimal('monetary_value', 15, 2)->nullable(); // القيمة المالية
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->integer('points')->nullable(); // النقاط
            $table->enum('status', ['pending', 'approved', 'awarded', 'cancelled'])->default('pending');
            $table->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete(); // من منح المكافأة
            $table->dateTime('awarded_at')->nullable();
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
        Schema::dropIfExists('employee_rewards');
    }
};
