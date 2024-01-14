<?php

use App\Http\Controllers\ExploreController;
use App\Http\Controllers\FollowsController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TweetDisLikeController;
use App\Http\Controllers\TweetLikeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [TweetController::class, 'index'])->name('home');
    Route::post('/tweets', [TweetController::class, 'store'])->name('tweet.store');

    Route::post('/tweets/{tweet}/like', [TweetLikeController::class, 'store']);
    Route::post('/tweets/{tweet}/dislike', [TweetDisLikeController::class, 'store']);

    Route::post('/{user:username}/follow', [FollowsController::class, 'store'])->name('follow.store');
    Route::delete('/{user:username}/follow', [FollowsController::class, 'destroy'])->name('follow.destroy');

    Route::get('/profiles/{user:username}/followers', [FollowsController::class, 'show'])->name('followers');
    Route::get('/profiles/{user:username}/following', [FollowsController::class, 'show'])->name('following');
    Route::get('/profiles/{user:username}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/explore', ExploreController::class)->name('explore');
});

Route::middleware('auth')->group(function() {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/send-emails', function () {
        $emails = \App\Models\User::pluck('email');
        foreach($emails as $email) {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\AdminMail());
        }

       return back();
    });
});

Route::get('/test', function() {
    return view('test');
})->middleware('auth');

Route::post('/test', function() {
    return event(new \App\Models\Notification('Hello, My World!'));
})->middleware('auth');

require __DIR__.'/auth.php';
