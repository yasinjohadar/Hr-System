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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete(); // الأصل
            $table->string('maintenance_type'); // نوع الصيانة (دورية، إصلاح، تنظيف)
            $table->date('scheduled_date')->nullable(); // تاريخ الصيانة المجدول
            $table->date('actual_date')->nullable(); // تاريخ الصيانة الفعلي
            $table->decimal('cost', 10, 2)->default(0); // التكلفة
            $table->text('description')->nullable(); // الوصف
            $table->text('work_done')->nullable(); // العمل المنجز
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled'); // الحالة
            $table->date('next_maintenance_date')->nullable(); // تاريخ الصيانة القادمة
            $table->string('service_provider')->nullable(); // مزود الخدمة
            $table->text('notes')->nullable(); // ملاحظات
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
        Schema::dropIfExists('asset_maintenances');
    }
};
