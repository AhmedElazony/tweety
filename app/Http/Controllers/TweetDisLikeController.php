<?php

namespace App\Http\Controllers;

use App\Models\Tweet;

class TweetDisLikeController extends Controller
{
    public function store(Tweet $tweet)
    {
        if (currentUser()->disLiked($tweet)) {
            return $this->destroy($tweet);
        }
        $wasLiked = currentUser()->liked($tweet);
        currentUser()->dislike($tweet);

        return response()->json([
            'success' => true,
            'wasLiked' => $wasLiked,
            'disliked' => true,
            'dislikesCount' => $tweet->likes()->where('liked', false)->count()
        ]);
    }

    public function destroy(Tweet $tweet)
    {
        currentUser()->unReact($tweet);

        return response()->json([
            'success' => true,
            'disliked' => false,
            'dislikesCount' => $tweet->likes()->where('liked', false)->count()
        ]);
    }
}
