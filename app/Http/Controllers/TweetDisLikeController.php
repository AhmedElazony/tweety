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

        if (currentUser()->liked($tweet)) {
            return back()->withErrors(['tweet' => 'you can not like and dislike the same tweet!']);
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
