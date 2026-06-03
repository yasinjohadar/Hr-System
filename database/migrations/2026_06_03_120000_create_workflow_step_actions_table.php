<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_step_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained('workflow_instances')->cascadeOnDelete();
            $table->foreignId('workflow_step_id')->constrained('workflow_steps')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action', ['approved', 'rejected']);
            $table->text('comments')->nullable();
            $table->dateTime('acted_at');
            $table->timestamps();

            $table->unique(['workflow_instance_id', 'workflow_step_id'], 'workflow_step_actions_instance_step_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_step_actions');
    }
};
