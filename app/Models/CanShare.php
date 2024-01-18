<?php

namespace App\Models;

trait CanShare
{
    public function share(Tweet $tweet): void
    {
        $this->shares()->updateOrCreate([
            'tweet_id' => $tweet->id,
            'user_id' => $this->id
        ], [
            'tweet_id' => $tweet->id,
            'user_id' => currentUser()->id
        ]);
    }

    public function unshare(Tweet $tweet): void
    {
        $sharedTweet = Share::where('tweet_id', '=', $tweet->id)
            ->where('user_id', '=', currentUser()->id);
        $sharedTweet->delete();
    }

    public function shared(Tweet $tweet): bool
    {
        return (bool) $this->shares()
            ->where('tweet_id', '=', $tweet->id)
            ->where('user_id', '=', $this->id)
            ->count();
    }
}
