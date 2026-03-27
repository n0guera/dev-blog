<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostStatus;
use App\Models\Tag;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoteType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('post belongs to user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    expect($post->user->is($user))->toBeTrue();
});

test('post belongs to status', function () {
    $status = PostStatus::factory()->published()->create();
    $post = Post::factory()->create(['status_id' => $status->id]);

    expect($post->status->is($status))->toBeTrue();
});

test('post has many tags', function () {
    $post = Post::factory()->create();
    $tag = Tag::factory()->create();

    $post->tags()->attach($tag);

    expect($post->tags->first()->is($tag))->toBeTrue();
});

test('post has many comments', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    expect($post->comments->first()->is($comment))->toBeTrue();
});

test('post has many votes', function () {
    $post = Post::factory()->create();
    $vote = Vote::factory()->create([
        'votable_type' => Post::class,
        'votable_id' => $post->id,
    ]);

    expect($post->votes->first()->is($vote))->toBeTrue();
});

test('post vote score calculates correctly with upvotes', function () {
    $post = Post::factory()->create();
    $upType = VoteType::factory()->up()->create();
    $user = User::factory()->create();

    Vote::factory()->create([
        'votable_type' => Post::class,
        'votable_id' => $post->id,
        'user_id' => $user->id,
        'vote_type_id' => $upType->id,
    ]);

    expect($post->fresh()->vote_score)->toBe(1);
});

test('post vote score with mixed votes', function () {
    $post = Post::factory()->create();
    $upType = VoteType::factory()->up()->create();
    $downType = VoteType::factory()->down()->create();

    Vote::factory()->create([
        'votable_type' => Post::class,
        'votable_id' => $post->id,
        'vote_type_id' => $upType->id,
    ]);

    Vote::factory()->create([
        'votable_type' => Post::class,
        'votable_id' => $post->id,
        'vote_type_id' => $downType->id,
    ]);

    Vote::factory()->create([
        'votable_type' => Post::class,
        'votable_id' => $post->id,
        'vote_type_id' => $upType->id,
    ]);

    expect($post->fresh()->vote_score)->toBe(1);
});

test('post can be soft deleted', function () {
    $post = Post::factory()->create();

    $post->delete();

    $this->assertSoftDeleted($post);
});
