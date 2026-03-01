<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\WorkflowInstance;

class ApprovalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public WorkflowInstance $instance;
    public string $entityType;
    public string $entityName;
    public string $employeeName;

    /**
     * Create a new notification instance.
     */
    public function __construct(WorkflowInstance $instance, string $entityType, string $entityName, string $employeeName)
    {
        $this->instance = $instance;
        $this->entityType = $entityType;
        $this->entityName = $entityName;
        $this->employeeName = $employeeName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->getTitle();
        $message = $this->getMessage();
        $url = $this->getActionUrl();

        return (new MailMessage)
                    ->subject($title)
                    ->line($message)
                    ->action('عرض الطلب', $url);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'approval_request',
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'message_ar' => $this->getMessageAr(),
            'action_url' => $this->getActionUrl(),
            'action_text' => 'عرض الطلب',
            'icon' => $this->getIcon(),
            'color' => 'warning',
            'data' => [
                'workflow_instance_id' => $this->instance->id,
                'entity_type' => $this->entityType,
                'entity_id' => $this->instance->entity_id,
                'workflow_type' => $this->instance->workflow->type ?? null,
            ],
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'type' => 'approval_request',
            'title' => $this->getTitle(),
            'message' => $this->getMessageAr(),
            'icon' => $this->getIcon(),
            'color' => 'warning',
            'action_url' => $this->getActionUrl(),
            'action_text' => 'عرض الطلب',
            'data' => [
                'workflow_instance_id' => $this->instance->id,
                'entity_type' => $this->entityType,
                'entity_id' => $this->instance->entity_id,
            ],
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get notification title
     */
    private function getTitle(): string
    {
        return match($this->entityType) {
            'LeaveRequest' => 'طلب موافقة على إجازة',
            'ExpenseRequest' => 'طلب موافقة على مصروف',
            'OvertimeRecord' => 'طلب موافقة على ساعات إضافية',
            'PerformanceReview' => 'طلب موافقة على تقييم أداء',
            default => 'طلب موافقة',
        };
    }

    /**
     * Get notification message
     */
    private function getMessage(): string
    {
        return "You have a pending approval request from {$this->employeeName}";
    }

    /**
     * Get notification message in Arabic
     */
    private function getMessageAr(): string
    {
        $entityTypeAr = match($this->entityType) {
            'LeaveRequest' => 'إجازة',
            'ExpenseRequest' => 'مصروف',
            'OvertimeRecord' => 'ساعات إضافية',
            'PerformanceReview' => 'تقييم أداء',
            default => 'طلب',
        };

        return "لديك طلب موافقة معلق من {$this->employeeName} - {$entityTypeAr}: {$this->entityName}";
    }

    /**
     * Get action URL
     */
    private function getActionUrl(): string
    {
        $type = match($this->entityType) {
            'LeaveRequest' => 'leave',
            'ExpenseRequest' => 'expense',
            'OvertimeRecord' => 'overtime',
            'PerformanceReview' => 'performance',
            default => 'approval',
        };

        return route('admin.approvals.show', ['type' => $type, 'id' => $this->instance->entity_id]);
    }

    /**
     * Get icon
     */
    private function getIcon(): string
    {
        return match($this->entityType) {
            'LeaveRequest' => 'fas fa-calendar-check',
            'ExpenseRequest' => 'fas fa-money-bill-wave',
            'OvertimeRecord' => 'fas fa-clock',
            'PerformanceReview' => 'fas fa-star',
            default => 'fas fa-bell',
        };
    }
}
