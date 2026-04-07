<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoteType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('vote belongs to user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $voteType = VoteType::factory()->up()->create();

    $vote = Vote::factory()->create([
        'user_id' => $user->id,
        'votable_type' => Post::class,
        'votable_id' => $post->id,
        'vote_type_id' => $voteType->id,
    ]);

    expect($vote->user->is($user))->toBeTrue();
});

test('vote belongs to vote type', function () {
    $post = Post::factory()->create();
    $voteType = VoteType::factory()->up()->create();

    $vote = Vote::factory()->create([
        'votable_type' => Post::class,
        'votable_id' => $post->id,
        'vote_type_id' => $voteType->id,
    ]);

    expect($vote->voteType->is($voteType))->toBeTrue();
});

test('vote morphs to post', function () {
    $post = Post::factory()->create();
    $voteType = VoteType::factory()->up()->create();

    $vote = Vote::factory()->create([
        'votable_type' => Post::class,
        'votable_id' => $post->id,
        'vote_type_id' => $voteType->id,
    ]);

    expect($vote->votable->is($post))->toBeTrue();
});

test('vote morphs to comment', function () {
    $comment = Comment::factory()->create();
    $voteType = VoteType::factory()->up()->create();

    $vote = Vote::factory()->create([
        'votable_type' => Comment::class,
        'votable_id' => $comment->id,
        'vote_type_id' => $voteType->id,
    ]);

    expect($vote->votable->is($comment))->toBeTrue();
});

test('vote type has up and down values', function () {
    $upType = VoteType::factory()->up()->create();
    $downType = VoteType::factory()->down()->create();

    expect($upType->name)->toBe('up');
    expect($downType->name)->toBe('down');
});
