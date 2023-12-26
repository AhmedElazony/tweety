<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function store(User $user)
    {
        auth()->user()->follow($user);

        return back();
    }

    public function destroy(User $user)
    {
        auth()->user()->unfollow($user);

        return back();
    }
}
