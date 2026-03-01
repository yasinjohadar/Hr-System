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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_code')->unique(); // كود المرشح
            
            // معلومات شخصية
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name');
            $table->string('national_id')->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('marital_status')->nullable();
            
            // معلومات الاتصال
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('postal_code')->nullable();
            
            // معلومات مهنية
            $table->string('current_position')->nullable(); // المنصب الحالي
            $table->string('current_company')->nullable(); // الشركة الحالية
            $table->integer('years_of_experience')->nullable(); // سنوات الخبرة
            $table->string('education_level')->nullable(); // المستوى التعليمي
            $table->string('university')->nullable(); // الجامعة
            $table->string('major')->nullable(); // التخصص
            $table->year('graduation_year')->nullable(); // سنة التخرج
            
            // الملفات
            $table->string('cv_path')->nullable(); // مسار السيرة الذاتية
            $table->string('cover_letter_path')->nullable(); // مسار خطاب التقديم
            $table->string('photo')->nullable(); // الصورة الشخصية
            
            // المهارات واللغات
            $table->text('skills')->nullable(); // المهارات (JSON أو نص)
            $table->text('languages')->nullable(); // اللغات (JSON أو نص)
            $table->text('certifications')->nullable(); // الشهادات
            
            // معلومات إضافية
            $table->text('notes')->nullable(); // ملاحظات
            $table->enum('status', ['new', 'contacted', 'screening', 'interviewed', 'offered', 'hired', 'rejected', 'withdrawn'])->default('new');
            $table->integer('rating')->nullable(); // التقييم (1-5)
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('candidate_code');
            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
