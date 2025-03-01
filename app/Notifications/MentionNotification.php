<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Tweet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class MentionNotification extends Notification
{
    public function __construct(private Tweet|Comment $tweetOrComment)
    {
    }

    public function via($notifiable)
    {
        return $notifiable->PushSubscriptions()->exists() ? [WebPushChannel::class, 'mail'] : ['mail'];
    }

    public function toWebPush($notifiable, $notification)
    {
        $author = $this->tweetOrComment->user->name;
        $messageText = $this->tweetOrComment->body;

        return (new WebPushMessage)
            ->title($author . ' Mentioned You!')
            ->icon('/notification-icon.png')
            ->body(strip_tags($messageText))
            ->action('View message', 'view_message')
            ->data(['message_id' => $this->tweetOrComment->id]);
    }

    public function toMail($notifiable)
    {
        $author = $this->tweetOrComment->user->name;
        $messageText = $this->tweetOrComment->body ?? "You've been mentioned!";

        return (new MailMessage)
            ->subject($author . ' Mentioned You!')
            ->line(strip_tags($messageText))
            ->action('View message', url('/tweets/' . $this->tweetOrComment->id));
    }
}
