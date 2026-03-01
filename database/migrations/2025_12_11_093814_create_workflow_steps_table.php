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
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->integer('step_order'); // ترتيب الخطوة
            $table->enum('approver_type', ['user', 'role', 'department_manager', 'employee_manager', 'custom'])->default('user');
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete(); // إذا كان approver_type = user
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete(); // إذا كان approver_type = role
            $table->boolean('is_required')->default(true); // هل الخطوة إلزامية
            $table->boolean('can_reject')->default(true); // هل يمكن رفض الطلب في هذه الخطوة
            $table->integer('timeout_hours')->nullable(); // عدد الساعات قبل انتهاء المهلة
            $table->text('conditions')->nullable(); // شروط الانتقال للخطوة التالية (JSON)
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['workflow_id', 'step_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
