<?php

namespace App\Console\Commands;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStepAction;
use App\Services\WorkflowService;
use Illuminate\Console\Command;

class BackfillWorkflowStepActions extends Command
{
    protected $signature = 'workflow:backfill-step-actions {--dry-run : Preview without writing}';

    protected $description = 'Create synthetic workflow_step_actions for completed steps on in-progress instances (legacy data)';

    public function handle(WorkflowService $workflowService): int
    {
        $dryRun = $this->option('dry-run');
        $created = 0;

        $instances = WorkflowInstance::where('status', 'in_progress')
            ->whereNotNull('workflow_step_id')
            ->with(['workflow.steps', 'currentStep'])
            ->get();

        foreach ($instances as $instance) {
            $current = $instance->currentStep;
            if (! $current) {
                continue;
            }

            foreach ($instance->workflow->steps as $step) {
                if ($step->step_order >= $current->step_order) {
                    continue;
                }

                if (WorkflowStepAction::where('workflow_instance_id', $instance->id)
                    ->where('workflow_step_id', $step->id)
                    ->exists()) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("Would backfill instance {$instance->id} step {$step->step_order}");
                    $created++;

                    continue;
                }

                WorkflowStepAction::create([
                    'workflow_instance_id' => $instance->id,
                    'workflow_step_id' => $step->id,
                    'user_id' => $instance->initiated_by ?? 1,
                    'action' => 'approved',
                    'comments' => null,
                    'acted_at' => $instance->started_at ?? now(),
                ]);
                $created++;
            }
        }

        $this->info($dryRun ? "Would create {$created} step action(s)." : "Created {$created} step action(s).");

        return self::SUCCESS;
    }
}
