<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tweet;
use App\Models\Comment;
use App\Notifications\MentionNotification;

class MentionService
{
    public function extractMentions(string $text): array
    {
        preg_match_all('/(?<=^|[^\w])@([\w]{1,30})/', $text, $matches);
        return $matches[1] ?? [];
    }

    public function notifyMentionedUsers(Tweet|Comment $content): void
    {
        $mentions = $this->extractMentions($content->body);

        if (empty($mentions)) {
            return;
        }

        // Find and notify mentioned users
        User::whereIn('username', $mentions)
            ->where('id', '!=', $content->user_id) // Don't notify the author
            ->get()
            ->each(function ($user) use ($content) {
                $user->notify(new MentionNotification($content));
            });
    }
}
