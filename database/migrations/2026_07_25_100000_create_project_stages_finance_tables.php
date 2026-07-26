<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('allocated_amount', 15, 2)->default(0);
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'sort_order']);
        });

        Schema::create('project_stage_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_stage_id')->constrained('project_stages')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['project_stage_id', 'employee_id']);
        });

        Schema::create('project_budget_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->decimal('previous_budget', 15, 2)->nullable();
            $table->decimal('requested_stages_total', 15, 2);
            $table->text('reason');
            $table->foreignId('approved_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('approved_at');
            $table->timestamps();
        });

        Schema::create('company_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_code')->unique();
            $table->enum('type', ['internal', 'disbursement', 'adjustment'])->default('internal');
            $table->string('from_account_type')->nullable(); // company | employee
            $table->unsignedBigInteger('from_account_id')->nullable();
            $table->string('to_account_type'); // company | employee
            $table->unsignedBigInteger('to_account_id');
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('project_stage_id')->nullable()->constrained('project_stages')->nullOnDelete();
            $table->enum('status', ['pending', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['from_account_type', 'from_account_id']);
            $table->index(['to_account_type', 'to_account_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_transfers');
        Schema::dropIfExists('company_bank_accounts');
        Schema::dropIfExists('project_budget_overrides');
        Schema::dropIfExists('project_stage_members');
        Schema::dropIfExists('project_stages');
    }
};
