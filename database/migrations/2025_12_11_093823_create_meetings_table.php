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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_code')->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable(); // رابط الاجتماع الافتراضي
            $table->enum('type', ['in_person', 'virtual', 'hybrid'])->default('in_person');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->foreignId('organizer_id')->nullable()->constrained('employees')->nullOnDelete(); // منظم الاجتماع
            $table->text('agenda')->nullable(); // جدول الأعمال
            $table->text('minutes')->nullable(); // محضر الاجتماع
            $table->text('action_items')->nullable(); // بنود العمل
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
        Schema::dropIfExists('meetings');
    }
};
