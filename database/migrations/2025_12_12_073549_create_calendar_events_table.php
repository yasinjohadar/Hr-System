<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الحدث
            $table->string('title_ar')->nullable(); // العنوان بالعربية
            $table->text('description')->nullable(); // وصف الحدث
            $table->dateTime('start_date'); // تاريخ ووقت البدء
            $table->dateTime('end_date')->nullable(); // تاريخ ووقت الانتهاء
            $table->enum('type', ['personal', 'public', 'department', 'employee', 'all'])->default('personal');
            // personal: شخصي للموظف
            // public: عام للمؤسسة
            // department: لقسم معين
            // employee: لموظف معين
            // all: للجميع
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); // منشئ الحدث
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete(); // للموظف المحدد
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete(); // للقسم المحدد
            $table->string('color')->default('#3b82f6'); // لون الحدث
            $table->boolean('is_all_day')->default(false); // حدث طوال اليوم
            $table->boolean('is_reminder')->default(false); // تذكير
            $table->integer('reminder_minutes')->nullable(); // دقائق قبل التذكير
            $table->timestamp('reminder_sent_at')->nullable(); // تاريخ إرسال التذكير
            $table->boolean('is_recurring')->default(false); // حدث متكرر
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly', 'yearly'])->nullable(); // نوع التكرار
            $table->integer('recurrence_interval')->nullable(); // فترة التكرار
            $table->date('recurrence_end_date')->nullable(); // تاريخ انتهاء التكرار
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
