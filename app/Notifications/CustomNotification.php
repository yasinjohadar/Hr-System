<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Events\NotificationSent;

class CustomNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $messageAr;
    public $type;
    public $actionUrl;
    public $actionText;
    public $icon;
    public $color;
    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $type = 'info', $actionUrl = null, $actionText = null, $icon = null, $color = 'info', $messageAr = null, $data = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->messageAr = $messageAr;
        $this->type = $type;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
        $this->icon = $icon;
        $this->color = $color;
        $this->data = $data;
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
        return (new MailMessage)
                    ->subject($this->title)
                    ->line($this->messageAr ?? $this->message)
                    ->action($this->actionText ?? 'عرض', $this->actionUrl ?? url('/'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'message_ar' => $this->messageAr,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'icon' => $this->icon,
            'color' => $this->color,
            'data' => $this->data,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->messageAr ?? $this->message,
            'icon' => $this->icon,
            'color' => $this->color,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
