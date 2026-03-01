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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable(); // الاسم بالعربية
            $table->string('code', 2)->unique(); // ISO 3166-1 alpha-2 (مثل: SA, US)
            $table->string('code3', 3)->unique()->nullable(); // ISO 3166-1 alpha-3 (مثل: SAU, USA)
            $table->string('phone_code', 10)->nullable(); // رمز الهاتف (مثل: +966)
            $table->string('currency_code', 3)->nullable(); // رمز العملة الافتراضية
            $table->string('flag')->nullable(); // رابط العلم أو emoji
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
        Schema::dropIfExists('countries');
    }
};
