<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function __invoke()
    {
        return view('tweets.explore', [
            'users' => User::where('id', '!=', currentUser()->id)
                ->paginate(30)
        ]);
    }
}
