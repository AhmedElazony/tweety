<?php

namespace App\Http\Controllers;

use App\Models\Tweet;

class TweetLikeController extends Controller
{
    public function store(Tweet $tweet)
    {
        if (currentUser()->liked($tweet)) {
            return $this->destroy($tweet);
        }
        $wasDisliked = currentUser()->disLiked($tweet);
        currentUser()->like($tweet);

        return response()->json([
            'success' => true,
            'wasDisliked' => $wasDisliked,
            'liked' => true,
            'likesCount' => $tweet->likes->count()
        ]);
    }

    public function destroy(Tweet $tweet)
    {
        currentUser()->unReact($tweet);

        return response()->json([
            'success' => true,
            'liked' => false,
            'likesCount' => $tweet->likes->count()
        ]);
    }
}
