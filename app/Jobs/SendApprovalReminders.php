<?php

namespace App\Jobs;

use App\Models\WorkflowInstance;
use App\Models\ApprovalReminder;
use App\Notifications\ApprovalReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendApprovalReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $this->sendFirstReminders();
        $this->sendSecondReminders();
    }

    protected function sendFirstReminders(): void
    {
        $instances = WorkflowInstance::where('status', 'in_progress')
            ->where('started_at', '<', now()->subHours(24))
            ->where('started_at', '>', now()->subHours(48))
            ->get();

        foreach ($instances as $instance) {
            $alreadySent = ApprovalReminder::where('workflow_instance_id', $instance->id)
                ->where('reminder_level', 1)
                ->exists();

            if ($alreadySent) continue;

            $approver = $this->getApprover($instance);
            if (!$approver) continue;

            try {
                $approver->notify(new ApprovalReminderNotification($instance, 1));

                ApprovalReminder::create([
                    'workflow_instance_id' => $instance->id,
                    'reminder_level' => 1,
                    'sent_at' => now(),
                    'sent_to' => $approver->id,
                    'channel' => 'database',
                ]);

                Log::info("First reminder sent to user {$approver->id} for instance {$instance->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send first reminder: {$e->getMessage()}");
            }
        }
    }

    protected function sendSecondReminders(): void
    {
        $instances = WorkflowInstance::where('status', 'in_progress')
            ->where('started_at', '<', now()->subHours(48))
            ->get();

        foreach ($instances as $instance) {
            $alreadySent = ApprovalReminder::where('workflow_instance_id', $instance->id)
                ->where('reminder_level', 2)
                ->exists();

            if ($alreadySent) continue;

            $approver = $this->getApprover($instance);
            if (!$approver) continue;

            try {
                $approver->notify(new ApprovalReminderNotification($instance, 2));

                ApprovalReminder::create([
                    'workflow_instance_id' => $instance->id,
                    'reminder_level' => 2,
                    'sent_at' => now(),
                    'sent_to' => $approver->id,
                    'channel' => 'email',
                ]);

                Log::info("Second reminder sent to user {$approver->id} for instance {$instance->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send second reminder: {$e->getMessage()}");
            }
        }
    }

    protected function getApprover(WorkflowInstance $instance): ?\App\Models\User
    {
        $step = $instance->currentStep;
        if (!$step) return null;

        $employee = $this->getEmployeeFromInstance($instance);
        if (!$employee) return null;

        return app(\App\Services\ApprovalService::class)->getApproverForStep($step, $employee);
    }

    protected function getEmployeeFromInstance(WorkflowInstance $instance): ?\App\Models\Employee
    {
        $entity = $instance->entity;
        if (!$entity) return null;

        if (method_exists($entity, 'employee')) {
            return $entity->employee;
        }

        if (isset($entity->employee_id)) {
            return \App\Models\Employee::find($entity->employee_id);
        }

        return null;
    }
}
