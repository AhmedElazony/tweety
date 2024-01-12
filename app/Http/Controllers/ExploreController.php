<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function __invoke()
    {
        $followings = currentUser()->following->pluck('id');

        $users = User::whereNotIN('id', $followings->merge(currentUser()->id));

        return view('tweets.explore', [
            'users' => $users->paginate(20)

        ]);
    }
}
