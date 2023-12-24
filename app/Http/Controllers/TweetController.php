<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;

class TweetController extends Controller
{
    public function store()
    {
        $attributes = request()->validate([
            'body' => 'required|max:225'
        ]);

        Tweet::create([
            'user_id' => auth()->id(),
             'body' => $attributes['body']
        ]);

        return redirect('/home')->with('success', 'Your tweet has been published!');
    }
}
