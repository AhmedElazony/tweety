<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function store(User $user)
    {
        currentUser()->follow($user);

        return back();
    }

    public function destroy(User $user)
    {
        currentUser()->unfollow($user);

        return back();
    }
}
