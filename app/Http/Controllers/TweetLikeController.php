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

        currentUser()->like($tweet);

        return back();
    }

    public function destroy(Tweet $tweet)
    {
        currentUser()->unReact($tweet);

        return back();
    }
}
