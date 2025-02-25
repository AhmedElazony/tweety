<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewMessageNotification extends Notification
{
    public function __construct(private $message)
    {
    }

    public function via($notifiable)
    {
        return $notifiable->PushSubscriptions()->exists() ? [WebPushChannel::class] : [];
    }

    public function toWebPush($notifiable, $notification)
    {
        $messageText = $this->message->body ?: 'New message received';
        $senderName = \App\Models\User::find($this->message->from_id)->name ?? 'Someone';

        return (new WebPushMessage)
            ->title('New Message from ' . $senderName)
            ->icon('/notification-icon.png')
            ->body($messageText)
            ->action('View message', 'view_message')
            ->data(['message_id' => $this->message->id]);
    }
}
