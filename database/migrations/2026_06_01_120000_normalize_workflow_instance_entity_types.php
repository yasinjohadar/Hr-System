<?php

use App\Support\WorkflowEntityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (WorkflowEntityType::legacyShortNames() as $short => $class) {
            DB::table('workflow_instances')
                ->where('entity_type', $short)
                ->update(['entity_type' => $class]);
        }
    }

    public function down(): void
    {
        foreach (WorkflowEntityType::legacyShortNames() as $short => $class) {
            DB::table('workflow_instances')
                ->where('entity_type', $class)
                ->update(['entity_type' => $short]);
        }
    }
};
