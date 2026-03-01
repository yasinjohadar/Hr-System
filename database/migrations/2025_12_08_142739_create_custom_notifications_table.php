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
        Schema::create('custom_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // نوع الإشعار (leave_request, attendance, salary, etc.)
            $table->string('title'); // عنوان الإشعار
            $table->text('message'); // رسالة الإشعار
            $table->text('message_ar')->nullable(); // رسالة الإشعار بالعربية
            
            // المستلم
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete(); // مستلم محدد
            $table->json('recipient_ids')->nullable(); // قائمة المستلمين (JSON array)
            $table->enum('recipient_type', ['user', 'role', 'all', 'department', 'branch'])->default('user');
            
            // الرابط والإجراء
            $table->string('action_url')->nullable(); // رابط الإجراء
            $table->string('action_text')->nullable(); // نص الإجراء
            $table->string('icon')->nullable(); // أيقونة الإشعار
            $table->string('color')->default('info'); // لون الإشعار (info, success, warning, danger)
            
            // البيانات الإضافية
            $table->json('data')->nullable(); // بيانات إضافية (JSON)
            $table->foreignId('related_id')->nullable(); // معرف السجل المرتبط
            $table->string('related_type')->nullable(); // نوع السجل المرتبط
            
            // الحالة
            $table->boolean('is_read')->default(false); // تم القراءة
            $table->timestamp('read_at')->nullable(); // تاريخ القراءة
            $table->boolean('is_important')->default(false); // إشعار مهم
            $table->boolean('is_sent')->default(false); // تم الإرسال
            $table->timestamp('sent_at')->nullable(); // تاريخ الإرسال
            
            // الإعدادات
            $table->boolean('send_email')->default(false); // إرسال بريد إلكتروني
            $table->boolean('send_sms')->default(false); // إرسال SMS
            $table->boolean('send_push')->default(true); // إرسال Push notification
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('type');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_notifications');
    }
};
