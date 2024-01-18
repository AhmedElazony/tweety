<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Tweet;

class TweetController extends Controller
{
    public function index()
    {
        $tweets = currentUser()->timeline();
        $sharedTweets = currentUser()->sharedTweetsTimeline();

        $timeline = $tweets->merge($sharedTweets);

        return view('tweets.index', [
            'tweets' => $timeline
        ]);
    }

    public function show(Tweet $tweet)
    {
        return view('tweets.show', [
            'tweet' => Tweet::where('id', '=', $tweet->id)
                ->withLikes()
                ->with('user')
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

    public function edit(Tweet $tweet)
    {
        return view('tweets.edit', [
            'tweet' => $tweet
        ]);
    }

    public function update(Tweet $tweet)
    {
        $attributes = request()->validate([
            'body' => 'required|max:225'
        ]);

        $tweet->update($attributes);

        return redirect('/tweets/'.$tweet->id);
    }

    public function destroy(Tweet $tweet)
    {
        $tweet->delete();

        return back()->with('success', 'tweet deleted!');
    }
}
