<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\MentionService;

class CommentController extends Controller
{
    public function store()
    {
        $attributes = request()->validate([
            'body' => 'string|max:255',
            'tweet_id' => 'integer|exists:tweets,id',
            'user_id' => 'integer|exists:users,id'
        ]);

        $comment = Comment::create($attributes);

        app(MentionService::class)->notifyMentionedUsers($comment);

        return back();
    }
}
