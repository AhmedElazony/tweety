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

        currentUser()->dislike($tweet);

        return back();
    }

    public function destroy(Tweet $tweet)
    {
        currentUser()->unReact($tweet);

        return back();
    }
}
