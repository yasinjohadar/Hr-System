<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('status', 32)->default('open')->change();
            });
        } else {
            DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
                'pending_approval',
                'open',
                'in_progress',
                'resolved',
                'closed',
                'cancelled',
                'rejected'
            ) NOT NULL DEFAULT 'open'");
        }

        Schema::table('project_time_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('project_time_entries', 'status')) {
                $table->string('status', 32)->default('pending')->after('description');
            }
            if (! Schema::hasColumn('project_time_entries', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('project_time_entries', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_time_entries', function (Blueprint $table) {
            if (Schema::hasColumn('project_time_entries', 'approved_by')) {
                $table->dropForeign(['approved_by']);
            }
            $cols = array_filter([
                Schema::hasColumn('project_time_entries', 'status') ? 'status' : null,
                Schema::hasColumn('project_time_entries', 'approved_at') ? 'approved_at' : null,
                Schema::hasColumn('project_time_entries', 'approved_by') ? 'approved_by' : null,
            ]);
            if ($cols) {
                $table->dropColumn($cols);
            }
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
                'open',
                'in_progress',
                'resolved',
                'closed',
                'cancelled'
            ) NOT NULL DEFAULT 'open'");
        }
    }
};
