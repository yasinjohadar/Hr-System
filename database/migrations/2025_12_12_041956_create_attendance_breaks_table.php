<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->string('break_type')->default('lunch'); // نوع الاستراحة (lunch, coffee, prayer, other)
            $table->time('break_start'); // وقت بدء الاستراحة
            $table->time('break_end')->nullable(); // وقت انتهاء الاستراحة
            $table->integer('duration_minutes')->default(0); // مدة الاستراحة بالدقائق
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_breaks');
    }
};
