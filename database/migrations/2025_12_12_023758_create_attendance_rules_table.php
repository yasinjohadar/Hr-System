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
        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code')->unique(); // كود القاعدة
            $table->string('name'); // اسم القاعدة
            $table->string('name_ar')->nullable(); // اسم القاعدة بالعربية
            
            // نوع القاعدة
            $table->enum('rule_type', ['late', 'absent', 'early_leave', 'overtime', 'break', 'holiday']); // نوع القاعدة
            
            // الشروط
            $table->integer('threshold_minutes')->default(0); // الحد الأدنى بالدقائق
            $table->enum('action_type', ['warning', 'deduction', 'notification', 'block']); // نوع الإجراء
            $table->decimal('deduction_amount', 10, 2)->nullable(); // مبلغ الخصم (إذا كان action_type = deduction)
            $table->integer('deduction_percentage')->nullable(); // نسبة الخصم (إذا كان action_type = deduction)
            
            // التطبيق
            $table->boolean('apply_to_all')->default(false); // يطبق على جميع الموظفين
            $table->json('applicable_positions')->nullable(); // المناصب المناسبة
            $table->json('applicable_departments')->nullable(); // الأقسام المناسبة
            
            // الإشعارات
            $table->boolean('send_notification')->default(true); // إرسال إشعار
            $table->integer('notification_delay_minutes')->default(0); // تأخير الإشعار (بالدقائق)
            
            // الحالة
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // الأولوية (الأعلى أولاً)
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // فهارس
            $table->index(['rule_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_rules');
    }
};
