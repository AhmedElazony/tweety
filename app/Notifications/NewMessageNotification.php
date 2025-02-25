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
    use Queueable;

    public function __construct(private $message)
    {
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('New Message')
            ->icon('/notification-icon.png')
            ->body($this->message)
            ->action('View message', 'view_message')
            ->data(['message_id' => $this->message->id]);
    }
}
