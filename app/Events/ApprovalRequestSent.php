<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\WorkflowInstance;
use App\Models\User;

class ApprovalRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WorkflowInstance $instance;
    public User $approver;
    public string $entityType;
    public string $entityName;
    public string $employeeName;

    /**
     * Create a new event instance.
     */
    public function __construct(
        WorkflowInstance $instance,
        User $approver,
        string $entityType,
        string $entityName,
        string $employeeName
    ) {
        $this->instance = $instance;
        $this->approver = $approver;
        $this->entityType = $entityType;
        $this->entityName = $entityName;
        $this->employeeName = $employeeName;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // بث على القناة الخاصة بالمستخدم الموافق
        return [
            new PrivateChannel('user.' . $this->approver->id),
            new Channel('approvals'), // قناة عامة للموافقات
        ];
    }

    /**
     * اسم الحدث للبث
     */
    public function broadcastAs(): string
    {
        return 'approval.request';
    }

    /**
     * البيانات المرسلة مع الحدث
     */
    public function broadcastWith(): array
    {
        $type = match($this->entityType) {
            'LeaveRequest' => 'leave',
            'ExpenseRequest' => 'expense',
            'OvertimeRecord' => 'overtime',
            'PerformanceReview' => 'performance',
            default => 'approval',
        };

        $entityTypeAr = match($this->entityType) {
            'LeaveRequest' => 'إجازة',
            'ExpenseRequest' => 'مصروف',
            'OvertimeRecord' => 'ساعات إضافية',
            'PerformanceReview' => 'تقييم أداء',
            default => 'طلب',
        };

        return [
            'id' => $this->instance->id,
            'workflow_instance_id' => $this->instance->id,
            'entity_type' => $this->entityType,
            'entity_id' => $this->instance->entity_id,
            'type' => $type,
            'title' => "طلب موافقة على {$entityTypeAr}",
            'message' => "لديك طلب موافقة معلق من {$this->employeeName} - {$entityTypeAr}: {$this->entityName}",
            'employee_name' => $this->employeeName,
            'entity_name' => $this->entityName,
            'icon' => match($this->entityType) {
                'LeaveRequest' => 'fas fa-calendar-check',
                'ExpenseRequest' => 'fas fa-money-bill-wave',
                'OvertimeRecord' => 'fas fa-clock',
                'PerformanceReview' => 'fas fa-star',
                default => 'fas fa-bell',
            },
            'color' => 'warning',
            'action_url' => route('admin.approvals.show', ['type' => $type, 'id' => $this->instance->entity_id]),
            'action_text' => 'عرض الطلب',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
