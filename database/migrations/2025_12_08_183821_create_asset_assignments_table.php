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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete(); // الأصل
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete(); // الموظف
            $table->date('assigned_date'); // تاريخ التوزيع
            $table->date('expected_return_date')->nullable(); // تاريخ الاسترجاع المتوقع
            $table->date('actual_return_date')->nullable(); // تاريخ الاسترجاع الفعلي
            $table->enum('assignment_status', ['active', 'returned', 'lost', 'damaged'])->default('active'); // حالة التوزيع
            $table->enum('condition_on_assignment', ['excellent', 'good', 'fair', 'poor'])->default('excellent'); // حالة الأصل عند التوزيع
            $table->enum('condition_on_return', ['excellent', 'good', 'fair', 'poor', 'damaged'])->nullable(); // حالة الأصل عند الاسترجاع
            $table->text('assignment_notes')->nullable(); // ملاحظات التوزيع
            $table->text('return_notes')->nullable(); // ملاحظات الاسترجاع
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete(); // من وزع
            $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete(); // من استرجع
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // منشئ السجل
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
