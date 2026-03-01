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
        Schema::create('reward_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['monetary', 'non_monetary', 'points', 'recognition', 'gift'])->default('recognition');
            $table->decimal('default_value', 15, 2)->nullable(); // القيمة الافتراضية (للمكافآت المالية)
            $table->integer('default_points')->nullable(); // النقاط الافتراضية
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_types');
    }
};
