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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // مفتاح الإعداد (مثل: site_name, email_from)
            $table->string('group')->default('general'); // مجموعة الإعدادات (general, email, sms, attendance, etc.)
            $table->string('label'); // التسمية
            $table->string('label_ar')->nullable(); // التسمية بالعربية
            $table->text('value')->nullable(); // القيمة
            $table->string('type')->default('text'); // نوع الحقل (text, textarea, number, boolean, select, file, etc.)
            $table->text('options')->nullable(); // خيارات للحقول من نوع select (JSON)
            $table->text('description')->nullable(); // وصف الإعداد
            $table->text('description_ar')->nullable(); // الوصف بالعربية
            $table->string('validation')->nullable(); // قواعد التحقق
            $table->boolean('is_required')->default(false); // إلزامي
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->boolean('is_active')->default(true); // نشط
            $table->timestamps();
            
            // Indexes
            $table->index('group');
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
