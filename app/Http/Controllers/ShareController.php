<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function __invoke(Tweet $tweet)
    {
        if (currentUser()->shared($tweet)) {
            currentUser()->unshare($tweet);
            return back();
        }

        currentUser()->share($tweet);
        return back();
    }
}
