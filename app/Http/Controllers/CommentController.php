<?php

namespace App\Http\Controllers;

use App\Models\Comment;

class CommentController extends Controller
{
    public function store()
    {
        $attributes = request()->validate([
            'body' => 'string|max:255',
            'tweet_id' => 'integer',
            'user_id' => 'integer'
        ]);

        Comment::create($attributes);

        return back();
    }
}
