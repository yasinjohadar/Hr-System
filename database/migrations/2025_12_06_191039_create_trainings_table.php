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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الدورة
            $table->string('title_ar')->nullable(); // العنوان بالعربية
            $table->string('code')->unique(); // كود الدورة
            $table->text('description')->nullable(); // الوصف
            $table->text('description_ar')->nullable(); // الوصف بالعربية
            $table->enum('type', ['internal', 'external', 'online', 'workshop'])->default('internal'); // نوع التدريب
            $table->string('provider')->nullable(); // مقدم التدريب
            $table->string('location')->nullable(); // مكان التدريب
            $table->date('start_date'); // تاريخ البدء
            $table->date('end_date'); // تاريخ الانتهاء
            $table->time('start_time')->nullable(); // وقت البدء
            $table->time('end_time')->nullable(); // وقت الانتهاء
            $table->integer('duration_hours')->default(0); // مدة التدريب بالساعات
            $table->integer('max_participants')->nullable(); // الحد الأقصى للمشاركين
            $table->decimal('cost', 10, 2)->default(0); // التكلفة
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete(); // العملة
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned'); // الحالة
            $table->foreignId('instructor_id')->nullable()->constrained('employees')->nullOnDelete(); // المدرب
            $table->text('objectives')->nullable(); // الأهداف
            $table->text('content')->nullable(); // المحتوى
            $table->text('materials')->nullable(); // المواد التدريبية
            $table->text('notes')->nullable(); // ملاحظات
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
        Schema::dropIfExists('trainings');
    }
};
