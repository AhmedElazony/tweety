<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;

class TweetController extends Controller
{
    public function index()
    {
        $tweets = currentUser()->timeline();

        return view('tweets.index', [
            'tweets' => $tweets
        ]);
    }

    public function show(Tweet $tweet)
    {
        return view('tweets.show', [
            'tweet' => Tweet::where('id', '=', $tweet->id)
                ->withLikes()
                ->with('comments')
                ->get()
                ->first()
        ]);
    }

    public function store()
    {
        $attributes = request()->validate([
            'body' => 'required|max:225'
        ]);

        $tweet = Tweet::create([
            'user_id' => auth()->id(),
            'body' => $attributes['body']
        ]);

        event(new \App\Events\MessageNotification($tweet->user, 'Published a Tweet'));
        return redirect('/home')->with('success', 'Your tweet has been published!');
    }
}
