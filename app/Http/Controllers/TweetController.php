<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\User;
use App\Services\MentionService;
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
                ->with('user')
                ->with('comments')
                ->get()
                ->first()
        ]);
    }

    public function store()
    {
        $attributes = request()->validate([
            'body' => 'required|max:1024'
        ]);

        $tweet = Tweet::create([
            'user_id' => auth()->id(),
            'body' => $attributes['body']
        ]);

        app(MentionService::class)->notifyMentionedUsers($tweet);

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
            'body' => 'required|max:1024'
        ]);

        $tweet->update($attributes);

        return redirect('/tweets/' . $tweet->id);
    }

    public function destroy(Tweet $tweet)
    {
        $tweet->delete();

        return back()->with('success', 'tweet deleted!');
    }
}
