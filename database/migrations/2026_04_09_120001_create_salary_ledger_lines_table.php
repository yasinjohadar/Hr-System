<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_ledger_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->constrained('salaries')->cascadeOnDelete();
            $table->enum('line_type', [
                'allowance',
                'bonus',
                'deduction',
                'advance_recovery',
                'loan_installment',
                'overtime',
                'other',
            ]);
            $table->string('label')->nullable();
            $table->string('label_ar')->nullable();
            $table->decimal('amount', 12, 2);
            $table->foreignId('employee_advance_id')->nullable()->constrained('employee_advances')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['salary_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_ledger_lines');
    }
};
