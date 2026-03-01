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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم نوع الإجازة
            $table->string('name_ar')->nullable(); // الاسم بالعربية
            $table->string('code')->unique(); // كود فريد
            $table->text('description')->nullable(); // الوصف
            $table->integer('max_days')->nullable(); // الحد الأقصى للأيام في السنة
            $table->boolean('is_paid')->default(true); // مدفوعة أم لا
            $table->boolean('requires_approval')->default(true); // تحتاج موافقة
            $table->boolean('carry_forward')->default(false); // يمكن ترحيلها للعام القادم
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->boolean('is_active')->default(true); // نشط
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
