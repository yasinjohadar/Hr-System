<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\WorkflowInstance;

class ApprovalReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $instance;
    public $reminderLevel;

    public function __construct(WorkflowInstance $instance, int $reminderLevel)
    {
        $this->instance = $instance;
        $this->reminderLevel = $reminderLevel;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($this->reminderLevel >= 2) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entityType = $this->instance->entity_type;
        $entityTypeAr = match($entityType) {
            'App\Models\LeaveRequest' => 'طلب إجازة',
            'App\Models\ExpenseRequest' => 'طلب مصروفات',
            default => 'طلب',
        };

        $subject = match($this->reminderLevel) {
            1 => "تذكير: $entityTypeAr معلق",
            2 => "تذكير أخير: $entityTypeAr معلق منذ 48 ساعة",
            default => "تنبيه: $entityTypeAr معلق",
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('مرحباً،')
            ->line("لديك $entityTypeAr معلق بانتظار موافقتك.")
            ->line("مستوى التذكير: {$this->reminderLevel}")
            ->line("منذ: " . $this->instance->started_at->diffForHumans())
            ->action('عرض الطلب', url('/admin/approvals'))
            ->line('يرجى مراجعة الطلب واتخاذ الإجراء المناسب.');
    }

    public function toArray(object $notifiable): array
    {
        $entityType = $this->instance->entity_type;
        $entityTypeAr = match($entityType) {
            'App\Models\LeaveRequest' => 'طلب إجازة',
            'App\Models\ExpenseRequest' => 'طلب مصروفات',
            default => 'طلب',
        };

        $title = match($this->reminderLevel) {
            1 => "تذكير: $entityTypeAr معلق",
            2 => "تذكير أخير: $entityTypeAr معلق",
            default => "تنبيه: $entityTypeAr معلق",
        };

        return [
            'type' => 'approval_reminder',
            'title' => $title,
            'message' => "$entityTypeAr معلق بانتظار موافقتك منذ " . $this->instance->started_at->diffForHumans(),
            'action_url' => route('admin.approvals.show', ['type' => 'leave', 'id' => $this->instance->entity_id]),
            'icon' => 'ri-time-line',
            'color' => $this->reminderLevel >= 2 ? 'danger' : 'warning',
            'data' => [
                'reminder_level' => $this->reminderLevel,
                'workflow_instance_id' => $this->instance->id,
                'entity_type' => $entityType,
                'entity_id' => $this->instance->entity_id,
            ],
        ];
    }
}
