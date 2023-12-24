<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $tweets = auth()->user()->timeline();

        return view('home', [
            'tweets' => $tweets
        ]);
    }
}
