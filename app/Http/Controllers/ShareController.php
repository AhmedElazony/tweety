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
            return response()->json([
                'success' => true,
                'shared' => false,
                'sharesCount' => $tweet->shares->count()
            ]);
        }

        currentUser()->share($tweet);
        return response()->json([
            'success' => true,
            'shared' => true,
            'sharesCount' => $tweet->shares->count()
        ]);
    }
}
