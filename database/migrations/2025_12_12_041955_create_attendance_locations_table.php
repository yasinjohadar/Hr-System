<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الموقع
            $table->string('name_ar')->nullable();
            $table->string('code')->unique(); // كود الموقع
            $table->decimal('latitude', 10, 8); // خط العرض
            $table->decimal('longitude', 11, 8); // خط الطول
            $table->integer('radius_meters')->default(100); // نصف القطر المسموح (بالمتر)
            $table->text('address')->nullable(); // العنوان
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('allowed_employees')->nullable(); // الموظفون المسموح لهم بالحضور في هذا الموقع
            $table->json('allowed_departments')->nullable(); // الأقسام المسموح لها
            $table->json('allowed_positions')->nullable(); // المناصب المسموح لها
            $table->boolean('require_location')->default(true); // يتطلب تحديد الموقع
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_locations');
    }
};
