<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $entityType;
    public $entityName;
    public $approvedBy;
    public $approvedAt;

    public function __construct(string $entityType, string $entityName, $approvedBy = null, $approvedAt = null)
    {
        $this->entityType = $entityType;
        $this->entityName = $entityName;
        $this->approvedBy = $approvedBy;
        $this->approvedAt = $approvedAt ?? now();
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entityTypeAr = $this->getEntityTypeAr();

        return (new MailMessage)
            ->subject("تمت الموافقة على $entityTypeAr")
            ->greeting('مرحباً،')
            ->line("تمت الموافقة على $entityTypeAr الخاص بك.")
            ->line("رقم/اسم الطلب: {$this->entityName}")
            ->line("تاريخ الموافقة: {$this->approvedAt->format('Y/m/d H:i')}")
            ->action('عرض التفاصيل', url('/employee/dashboard'))
            ->line('شكراً لاستخدامك نظام إدارة الموارد البشرية.');
    }

    public function toArray(object $notifiable): array
    {
        $entityTypeAr = $this->getEntityTypeAr();

        return [
            'type' => 'approval_completed',
            'title' => "تمت الموافقة على $entityTypeAr",
            'message' => "تمت الموافقة على $entityTypeAr الخاص بك: {$this->entityName}",
            'message_ar' => "تمت الموافقة على $entityTypeAr الخاص بك: {$this->entityName}",
            'action_url' => $this->getActionUrl(),
            'icon' => $this->getIcon(),
            'color' => 'success',
            'data' => [
                'entity_type' => $this->entityType,
                'entity_name' => $this->entityName,
                'approved_at' => $this->approvedAt->format('Y/m/d H:i'),
            ],
        ];
    }

    protected function getEntityTypeAr(): string
    {
        return match($this->entityType) {
            'App\Models\LeaveRequest' => 'طلب الإجازة',
            'App\Models\ExpenseRequest' => 'طلب المصروفات',
            'App\Models\EmployeeJobChange' => 'طلب تغيير الوظيفة',
            'App\Models\OvertimeRecord' => 'طلب العمل الإضافي',
            default => 'الطلب',
        };
    }

    protected function getActionUrl(): string
    {
        return match($this->entityType) {
            'App\Models\LeaveRequest' => route('employee.leaves'),
            'App\Models\ExpenseRequest' => route('employee.expense-requests'),
            default => route('employee.dashboard'),
        };
    }

    protected function getIcon(): string
    {
        return match($this->entityType) {
            'App\Models\LeaveRequest' => 'ri-sun-line',
            'App\Models\ExpenseRequest' => 'ri-money-dollar-circle-line',
            default => 'ri-checkbox-circle-line',
        };
    }
}
