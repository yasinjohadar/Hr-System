<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الضريبة (مثل: ضريبة الدخل)
            $table->string('name_ar')->nullable();
            $table->string('code')->unique(); // كود الضريبة
            $table->enum('type', ['income_tax', 'social_insurance', 'health_insurance', 'other'])->default('income_tax');
            $table->enum('calculation_method', ['percentage', 'slab', 'fixed'])->default('percentage');
            $table->decimal('rate', 5, 2)->default(0); // النسبة المئوية
            $table->decimal('min_amount', 12, 2)->nullable(); // الحد الأدنى للراتب
            $table->decimal('max_amount', 12, 2)->nullable(); // الحد الأقصى للراتب
            $table->json('slabs')->nullable(); // شرائح الضريبة [{min, max, rate}]
            $table->decimal('exemption_amount', 12, 2)->default(0); // مبلغ الإعفاء
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_settings');
    }
};
