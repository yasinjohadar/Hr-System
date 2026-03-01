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
        Schema::create('disciplinary_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('action_type', ['verbal_warning', 'written_warning', 'final_warning', 'deduction', 'suspension', 'termination']); // نوع الإجراء
            $table->integer('severity_level')->default(1); // مستوى الخطورة
            $table->decimal('deduction_amount', 15, 2)->nullable(); // مبلغ الخصم (إن وجد)
            $table->integer('suspension_days')->nullable(); // أيام الإيقاف (إن وجد)
            $table->boolean('requires_approval')->default(true); // يتطلب موافقة
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
        Schema::dropIfExists('disciplinary_actions');
    }
};
