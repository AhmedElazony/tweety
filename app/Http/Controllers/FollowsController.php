<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function show(User $user)
    {

        return view('profile.follows', [
            'users' => request()->routeIs('followers') ? $user->followers : $user->following
        ]);

    }

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
