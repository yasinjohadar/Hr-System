<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // بث الإشعار على قناة خاصة بالمستخدم
        if ($this->notification->user_id) {
            return [
                new PrivateChannel('user.' . $this->notification->user_id),
                new Channel('notifications'), // قناة عامة للإشعارات
            ];
        }
        
        return [new Channel('notifications')];
    }

    /**
     * اسم الحدث للبث
     */
    public function broadcastAs(): string
    {
        return 'notification.sent';
    }

    /**
     * البيانات المرسلة مع الحدث
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'user_id' => $this->notification->user_id ?? null,
            'type' => $this->notification->type ?? 'system',
            'title' => $this->notification->title ?? '',
            'message' => $this->notification->message_ar ?? $this->notification->message ?? '',
            'icon' => $this->notification->icon ?? 'fas fa-bell',
            'color' => $this->notification->color ?? 'info',
            'action_url' => $this->notification->action_url ?? null,
            'action_text' => $this->notification->action_text ?? 'عرض',
            'is_read' => $this->notification->is_read ?? false,
            'created_at' => isset($this->notification->created_at) ? $this->notification->created_at->toDateTimeString() : now()->toDateTimeString(),
        ];
    }
}
