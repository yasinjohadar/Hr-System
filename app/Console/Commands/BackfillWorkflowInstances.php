<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Workflow;
use App\Services\EmployeeRequestSubmissionService;
use App\Services\WorkflowService;
use Illuminate\Console\Command;

class BackfillWorkflowInstances extends Command
{
    protected $signature = 'workflow:backfill-instances
                            {--dry-run : List pending requests without creating instances}
                            {--strict : Require department manager like new submissions (legacy rows may fail)}';

    protected $description = 'Start workflow instances for pending employee requests that have no active instance';

    public function handle(EmployeeRequestSubmissionService $submissionService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $strict = (bool) $this->option('strict');
        $created = 0;
        $skipped = 0;
        $advanced = 0;
        $errors = 0;

        foreach ($submissionService->allTypes() as $workflowType => $config) {
            if (! Workflow::where('type', $workflowType)->where('is_active', true)->exists()) {
                $this->warn("No active workflow for type: {$workflowType}");
                continue;
            }

            $modelClass = $config['model'];
            $statusField = $config['status_field'];
            $pendingValues = $config['pending_values'];

            $requests = $modelClass::query()
                ->whereIn($statusField, $pendingValues)
                ->get();

            foreach ($requests as $request) {
                $existing = WorkflowService::findInstanceForEntity($modelClass, (int) $request->getKey());
                if ($existing) {
                    $skipped++;

                    continue;
                }

                $employee = Employee::find($request->employee_id);
                if (! $employee) {
                    $this->error("Missing employee for {$workflowType} #{$request->getKey()}");
                    $errors++;

                    continue;
                }

                if ($dryRun) {
                    $this->line("[dry-run] Would start {$workflowType} for entity #{$request->getKey()}");
                    $created++;

                    continue;
                }

                try {
                    $submissionService->afterRequestCreated(
                        $workflowType,
                        $employee,
                        $request,
                        requireDepartmentManager: $strict,
                        advancePastUnstaffedSteps: ! $strict
                    );
                    $this->info("Started {$workflowType} for entity #{$request->getKey()}");
                    $created++;
                    if (! $strict) {
                        $advanced++;
                    }
                } catch (\Throwable $e) {
                    $this->error("Failed {$workflowType} #{$request->getKey()}: {$e->getMessage()}");
                    $errors++;
                }
            }
        }

        $mode = $strict ? 'strict' : 'lenient (skip unstaffed dept steps)';
        $this->info("Done [{$mode}]. created={$created}, skipped={$skipped}, errors={$errors}");

        if (! $strict && $errors === 0 && $created > 0) {
            $this->comment('Tip: assign department managers in Admin → Departments for step-1 approvals on new requests.');
        }

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
