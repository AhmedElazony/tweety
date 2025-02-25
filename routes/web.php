<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\FollowsController;
use App\Http\Controllers\ShareController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [TweetController::class, 'index'])->name('home');
    Route::post('/tweets', [TweetController::class, 'store'])->name('tweet.store');

    Route::get('/tweets/{tweet}', [TweetController::class, 'show'])->name('tweet.show');
    Route::get('/tweets/{tweet}/edit', [TweetController::class, 'edit'])->name('tweet.edit');
    Route::put('/tweets/{tweet}/update', [TweetController::class, 'update'])->name('tweet.update');
    Route::post('/tweets/{tweet}/share', ShareController::class)->name('tweet.share');
    Route::delete('/tweets/{tweet}/delete', [TweetController::class, 'destroy'])->name('tweet.destroy');

    Route::post('/comments', [CommentController::class, 'store'])->name('comment.store');

    Route::post('/tweets/{tweet}/like', [TweetLikeController::class, 'store']);
    Route::post('/tweets/{tweet}/dislike', [TweetDisLikeController::class, 'store']);

    Route::post('/{user:username}/follow', [FollowsController::class, 'store'])->name('follow.store');
    Route::delete('/{user:username}/follow', [FollowsController::class, 'destroy'])->name('follow.destroy');

    Route::get('/profiles/{user:username}/followers', [FollowsController::class, 'show'])->name('followers');
    Route::get('/profiles/{user:username}/following', [FollowsController::class, 'show'])->name('following');
    Route::get('/profiles/{user:username}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/explore', ExploreController::class)->name('explore');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/push-subscription', function (Request $request) {
    $request->user()->updatePushSubscription(
        $request->input('endpoint'),
        $request->input('keys.p256dh'),
        $request->input('keys.auth')
    );

    return response()->json(['success' => true]);
})->middleware('auth');

require __DIR__ . '/auth.php';
