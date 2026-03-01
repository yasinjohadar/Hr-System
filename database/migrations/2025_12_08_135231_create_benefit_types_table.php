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
        Schema::create('benefit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الميزة بالإنجليزية
            $table->string('name_ar')->nullable(); // اسم الميزة بالعربية
            $table->string('code')->unique(); // كود الميزة
            $table->text('description')->nullable(); // الوصف
            $table->text('description_ar')->nullable(); // الوصف بالعربية
            
            // نوع الميزة
            $table->enum('type', ['monetary', 'in_kind', 'service', 'insurance', 'allowance'])->default('monetary');
            // monetary: نقدي (مثل بدل سكن)
            // in_kind: عيني (مثل سيارة)
            // service: خدمة (مثل تأمين صحي)
            // insurance: تأمين
            // allowance: بدل
            
            // القيمة الافتراضية
            $table->decimal('default_value', 10, 2)->nullable(); // القيمة الافتراضية
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            
            // معلومات إضافية
            $table->boolean('is_taxable')->default(false); // خاضع للضريبة
            $table->boolean('is_mandatory')->default(false); // إلزامي لجميع الموظفين
            $table->boolean('requires_approval')->default(false); // يتطلب موافقة
            $table->integer('max_employees')->nullable(); // الحد الأقصى للموظفين
            
            // الحالة
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // ترتيب العرض
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benefit_types');
    }
};
