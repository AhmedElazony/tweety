<?php

namespace App\Http\Controllers;

use App\Models\Comment;

class CommentController extends Controller
{
    public function store()
    {
        $attributes = request()->validate([
            'body' => 'string|max:255',
            'tweet_id' => 'integer|exists:tweets,id',
            'user_id' => 'integer|exists:users,id'
        ]);

        Comment::create($attributes);

        return back();
    }
}
