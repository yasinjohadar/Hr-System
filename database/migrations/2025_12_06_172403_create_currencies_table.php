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
        if (Schema::hasTable('currencies')) {
            return;
        }

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable(); // الاسم بالعربية
            $table->string('code', 3)->unique(); // ISO 4217 (مثل: SAR, USD)
            $table->string('symbol')->nullable(); // الرمز (مثل: ر.س, $)
            $table->string('symbol_ar')->nullable(); // الرمز بالعربية
            $table->integer('decimal_places')->default(2); // عدد الأرقام العشرية
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); // سعر الصرف مقابل العملة الأساسية
            $table->boolean('is_base_currency')->default(false); // العملة الأساسية
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
