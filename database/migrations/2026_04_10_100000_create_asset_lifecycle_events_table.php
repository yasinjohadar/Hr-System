<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_lifecycle_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('event_type', 64)->index();
            $table->dateTime('occurred_at');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('related_assignment_id')->nullable()->constrained('asset_assignments')->nullOnDelete();
            $table->foreignId('related_maintenance_id')->nullable()->constrained('asset_maintenances')->nullOnDelete();
            $table->string('summary', 512)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['asset_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_lifecycle_events');
    }
};
