<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE workflows MODIFY COLUMN type VARCHAR(64) NOT NULL DEFAULT 'custom'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE workflows MODIFY COLUMN type ENUM(
            'leave_request',
            'expense_request',
            'task_approval',
            'performance_review',
            'custom'
        ) NOT NULL DEFAULT 'custom'");
    }
};
