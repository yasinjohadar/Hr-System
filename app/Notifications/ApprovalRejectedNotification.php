<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $entityType;
    public $entityName;
    public $rejectionReason;
    public $rejectedBy;
    public $rejectedAt;

    public function __construct(string $entityType, string $entityName, ?string $rejectionReason = null, $rejectedBy = null, $rejectedAt = null)
    {
        $this->entityType = $entityType;
        $this->entityName = $entityName;
        $this->rejectionReason = $rejectionReason;
        $this->rejectedBy = $rejectedBy;
        $this->rejectedAt = $rejectedAt ?? now();
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entityTypeAr = $this->getEntityTypeAr();

        $mail = (new MailMessage)
            ->subject("تم رفض $entityTypeAr")
            ->greeting('مرحباً،')
            ->line("نأسف لإبلاغك بأنه تم رفض $entityTypeAr الخاص بك.")
            ->line("رقم/اسم الطلب: {$this->entityName}")
            ->line("تاريخ الرفض: {$this->rejectedAt->format('Y/m/d H:i')}");

        if ($this->rejectionReason) {
            $mail->line("سبب الرفض: {$this->rejectionReason}");
        }

        $mail->action('عرض التفاصيل', url('/employee/dashboard'))
            ->line('يمكنك تقديم طلب جديد بعد مراجعة السبب.');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $entityTypeAr = $this->getEntityTypeAr();

        return [
            'type' => 'approval_rejected',
            'title' => "تم رفض $entityTypeAr",
            'message' => "تم رفض $entityTypeAr الخاص بك: {$this->entityName}",
            'message_ar' => "تم رفض $entityTypeAr الخاص بك: {$this->entityName}",
            'action_url' => $this->getActionUrl(),
            'icon' => $this->getIcon(),
            'color' => 'danger',
            'data' => [
                'entity_type' => $this->entityType,
                'entity_name' => $this->entityName,
                'rejection_reason' => $this->rejectionReason,
                'rejected_at' => $this->rejectedAt->format('Y/m/d H:i'),
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
            default => 'ri-close-circle-line',
        };
    }
}
