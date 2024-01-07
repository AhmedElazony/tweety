<?php

namespace App\Models;

trait CanLike
{
    public function like(Tweet $tweet, $liked = true): void
    {
        $this->likes()->updateOrCreate([
            'user_id' => currentUser()->id,
            'tweet_id' => $tweet->id
        ], [
            'tweet_id' => $tweet->id,
            'liked' => $liked
        ]);
    }

    public function dislike(Tweet $tweet): void
    {
        $this->like($tweet, false);
    }

    public function liked(Tweet $tweet): bool
    {
        return (bool) $this->likes
            ->where('tweet_id', $tweet->id)
            ->where('liked', true)
            ->count();
    }

    public function disLiked(Tweet $tweet): bool
    {
        return (bool) $this->likes
            ->where('tweet_id', $tweet->id)
            ->where('liked', false)
            ->count();
    }

    public function unReact(Tweet $tweet): void
    {
        currentUser()->likes()->where('tweet_id', '=', $tweet->id)->delete();
    }
}
