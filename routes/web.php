<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\FollowsController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TweetDisLikeController;
use App\Http\Controllers\TweetLikeController;
use Illuminate\Http\Request;
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

Route::get('/push/key', function () {
    return response()->json([
        'key' => config('webpush.vapid.public_key')
    ]);
});

Route::post('/push/subscribe', static function (Request $request) {
    $request->validate([
        'subscription' => 'required',
    ]);

    $subscription = $request->subscription;
    auth()->user()->updatePushSubscription(
        $subscription['endpoint'],
        $subscription['keys']['p256dh'],
        $subscription['keys']['auth']
    );

    return response()->json(['success' => true]);
});

Route::get('/test-notification', function () {
    $user = Auth::user();
    $message = new \App\Models\ChMessage([
        'from_id' => 7,
        'to_id' => $user->id,
        'body' => 'Test notification message'
    ]);

    $user->notify(new \App\Notifications\NewMessageNotification($message));

    return 'Notification sent!';
});

Route::get('/check-vapid-key', function () {
    $publicKey = config('webpush.vapid.public_key');
    $privateKey = config('webpush.vapid.private_key');

    return response()->json([
        'public_key' => $publicKey,
        'public_key_length' => strlen($publicKey),
        'private_key_length' => strlen($privateKey),
        'public_key_valid_format' => (bool) preg_match('/^[A-Za-z0-9\-_]+$/', $publicKey),
    ]);
});

require __DIR__ . '/auth.php';
