<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\CustomNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendContractExpiryReminders extends Command
{
    protected $signature = 'contracts:send-expiry-reminders';

    protected $description = 'Send notifications for contracts expiring in 30, 60, or 90 days';

    public function handle(): int
    {
        $today = Carbon::today();
        $reminderDays = [30, 60, 90];
        $sent = 0;

        foreach ($reminderDays as $days) {
            $contracts = Contract::active()
                ->expiringInDays($days)
                ->where(function ($q) use ($today) {
                    $q->whereNull('reminder_sent_at')
                        ->orWhereDate('reminder_sent_at', '<', $today);
                })
                ->with('employee')
                ->get();

            foreach ($contracts as $contract) {
                $daysRemaining = (int) $today->diffInDays($contract->end_date, false);
                $employeeName = $contract->employee->full_name ?? $contract->employee->employee_code;
                $title = 'تذكير: عقد ينتهي قريباً';
                $message = "عقد الموظف {$employeeName} ينتهي خلال {$daysRemaining} يوماً (تاريخ الانتهاء: {$contract->end_date->format('Y-m-d')})";
                $actionUrl = route('admin.contracts.show', $contract);

                CustomNotification::create([
                    'type' => 'contract_expiry_reminder',
                    'title' => $title,
                    'message' => $message,
                    'message_ar' => $message,
                    'user_id' => null,
                    'recipient_type' => 'all',
                    'recipient_ids' => [],
                    'action_url' => $actionUrl,
                    'action_text' => 'عرض العقد',
                    'icon' => 'fas fa-file-contract',
                    'color' => $daysRemaining <= 30 ? 'danger' : ($daysRemaining <= 60 ? 'warning' : 'info'),
                    'related_type' => Contract::class,
                    'related_id' => $contract->id,
                    'is_read' => false,
                    'is_important' => true,
                    'is_sent' => true,
                    'sent_at' => now(),
                    'created_by' => null,
                ]);

                $contract->update(['reminder_sent_at' => now()]);
                $sent++;
            }
        }

        $this->info("Sent {$sent} contract expiry reminder(s).");
        return self::SUCCESS;
    }
}
