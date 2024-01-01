<?php

use App\Http\Controllers\FollowsController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(function () {
    Route::get('/home', [TweetController::class, 'index'])->name('home');
    Route::post('/tweets', [TweetController::class, 'store'])->name('tweet.store');

    Route::post('/profiles/{user:username}/follow', [FollowsController::class, 'store'])->name('follow.store');
    Route::delete('/profiles/{user:username}/follow', [FollowsController::class, 'destroy'])->name('follow.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profiles/{user:username}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
