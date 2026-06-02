<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained('workflow_instances')->onDelete('cascade');
            $table->tinyInteger('reminder_level');
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('sent_to')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('channel', ['database', 'email', 'sms'])->default('database');
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->timestamps();

            $table->index(['workflow_instance_id', 'reminder_level']);
            $table->index(['sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_reminders');
    }
};
