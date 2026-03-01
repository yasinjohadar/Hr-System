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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_code')->unique(); // كود المناوبة
            $table->string('name'); // اسم المناوبة
            $table->string('name_ar')->nullable(); // اسم المناوبة بالعربية
            
            // أوقات المناوبة
            $table->time('start_time'); // وقت البدء
            $table->time('end_time'); // وقت الانتهاء
            $table->integer('duration_hours')->default(8); // مدة المناوبة بالساعات
            
            // قواعد الحضور
            $table->integer('grace_period_minutes')->default(15); // فترة السماح للتأخير (بالدقائق)
            $table->integer('break_duration_minutes')->default(60); // مدة الاستراحة (بالدقائق)
            $table->boolean('has_night_shift')->default(false); // مناوبة ليلية
            $table->time('night_shift_start')->nullable(); // بداية المناوبة الليلية
            $table->time('night_shift_end')->nullable(); // نهاية المناوبة الليلية
            
            // أيام الأسبوع
            $table->boolean('monday')->default(true);
            $table->boolean('tuesday')->default(true);
            $table->boolean('wednesday')->default(true);
            $table->boolean('thursday')->default(true);
            $table->boolean('friday')->default(true);
            $table->boolean('saturday')->default(false);
            $table->boolean('sunday')->default(false);
            
            // الساعات الإضافية
            $table->decimal('overtime_rate', 5, 2)->default(1.5); // معدل الساعات الإضافية (1.5 = 150%)
            $table->integer('overtime_threshold_minutes')->default(0); // الحد الأدنى للساعات الإضافية (بالدقائق)
            
            // الحالة
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // فهارس
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
