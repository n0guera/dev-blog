<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag:slug}', [PostController::class, 'tagged'])->name('posts.byTag');
Route::get('/search', [PostController::class, 'search'])->name('posts.search');

Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('comments.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/votes/{type}/{id}/up', [VoteController::class, 'upvote'])->name('votes.up');
    Route::post('/votes/{type}/{id}/down', [VoteController::class, 'downvote'])->name('votes.down');
    Route::delete('/votes/{type}/{id}', [VoteController::class, 'removeVote'])->name('votes.remove');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
