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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique(); // كود الأصل
            $table->string('name'); // اسم الأصل
            $table->string('name_ar')->nullable(); // اسم الأصل بالعربي
            $table->string('category')->nullable(); // الفئة (تقنية، مكتبية، متنقلة)
            $table->string('type')->nullable(); // النوع (لابتوب، هاتف، إلخ)
            $table->string('manufacturer')->nullable(); // الشركة المصنعة
            $table->string('model')->nullable(); // الموديل
            $table->string('serial_number')->nullable()->unique(); // الرقم التسلسلي
            $table->string('barcode')->nullable()->unique(); // الباركود
            $table->date('purchase_date')->nullable(); // تاريخ الشراء
            $table->decimal('purchase_cost', 10, 2)->default(0); // تكلفة الشراء
            $table->decimal('current_value', 10, 2)->nullable(); // القيمة الحالية
            $table->enum('status', ['available', 'assigned', 'maintenance', 'damaged', 'lost', 'disposed'])->default('available'); // الحالة
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete(); // الفرع
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete(); // القسم
            $table->date('warranty_expiry')->nullable(); // انتهاء الضمان
            $table->text('description')->nullable(); // الوصف
            $table->text('notes')->nullable(); // ملاحظات
            $table->string('image_path')->nullable(); // صورة الأصل
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
        Schema::dropIfExists('assets');
    }
};
