<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostStatus;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Collection;

it('user has role and posts relationship', function () {
    $user = User::factory()->create();
    expect($user->role)->toBeInstanceOf(Role::class);
    expect($user->posts)->toBeInstanceOf(Collection::class);
});

it('post relationships work correctly', function () {
    $post = Post::factory()->create();
    $tags = Tag::factory()->count(2)->create();
    $post->tags()->attach($tags->pluck('id'));
    Comment::factory()->count(2)->create(['post_id' => $post->id]);
    $post->refresh();
    expect($post->user)->toBeInstanceOf(User::class);
    expect($post->status)->toBeInstanceOf(PostStatus::class);
    expect($post->tags)->toHaveCount(2);
    expect($post->comments)->toHaveCount(2);
});

it('comment can have replies and votes', function () {
    $comment = Comment::factory()->create();
    $reply = Comment::factory()->reply($comment)->create();
    $vote = Vote::factory()->create([
        'votable_type' => Comment::class,
        'votable_id' => $comment->id,
    ]);
    $comment->refresh();
    expect($comment->replies->first()->id)->toBe($reply->id);
    expect($comment->votes->first()->id)->toBe($vote->id);
});
