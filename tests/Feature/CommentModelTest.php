<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoteType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('comment belongs to post', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    expect($comment->post->is($post))->toBeTrue();
});

test('comment belongs to user', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    expect($comment->user->is($user))->toBeTrue();
});

test('comment has parent relationship', function () {
    $parent = Comment::factory()->create();
    $reply = Comment::factory()->reply($parent)->create();

    expect($reply->parent->is($parent))->toBeTrue();
});

test('comment has many replies', function () {
    $parent = Comment::factory()->create();
    $reply = Comment::factory()->reply($parent)->create();

    expect($parent->replies->first()->is($reply))->toBeTrue();
});

test('comment has many votes', function () {
    $comment = Comment::factory()->create();
    $vote = Vote::factory()->create([
        'votable_type' => Comment::class,
        'votable_id' => $comment->id,
    ]);

    expect($comment->votes->first()->is($vote))->toBeTrue();
});

test('comment vote score calculates correctly', function () {
    $comment = Comment::factory()->create();
    $upType = VoteType::factory()->up()->create();
    $user = User::factory()->create();

    Vote::factory()->create([
        'votable_type' => Comment::class,
        'votable_id' => $comment->id,
        'user_id' => $user->id,
        'vote_type_id' => $upType->id,
    ]);

    expect($comment->fresh()->vote_score)->toBe(1);
});

test('comment can be soft deleted', function () {
    $comment = Comment::factory()->create();

    $comment->delete();

    $this->assertSoftDeleted($comment);
});
