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

        if (currentUser()->disLiked($tweet)) {
            return back()->withErrors(['tweet' => 'you can not like an dislike the same tweet']);
        }

        currentUser()->like($tweet);

        return back();
    }

    public function destroy(Tweet $tweet)
    {
        currentUser()->unReact($tweet);

        return back();
    }
}
