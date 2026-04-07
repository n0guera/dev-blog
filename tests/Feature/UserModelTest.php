<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user belongs to role', function () {
    $role = Role::factory()->admin()->create();
    $user = User::factory()->create(['role_id' => $role->id]);

    expect($user->role->is($role))->toBeTrue();
});

test('user has many posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    expect($user->posts->first()->is($post))->toBeTrue();
});

test('user has many comments', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    expect($user->comments->first()->is($comment))->toBeTrue();
});

test('user has many votes', function () {
    $user = User::factory()->create();
    $vote = Vote::factory()->create(['user_id' => $user->id]);

    expect($user->votes->first()->is($vote))->toBeTrue();
});

test('isAdmin returns true for admin user', function () {
    $role = Role::factory()->admin()->create();
    $user = User::factory()->create(['role_id' => $role->id]);

    expect($user->isAdmin())->toBeTrue();
});

test('isAdmin returns false for regular user', function () {
    $role = Role::factory()->user()->create();
    $user = User::factory()->create(['role_id' => $role->id]);

    expect($user->isAdmin())->toBeFalse();
});

test('isAdmin returns false when user has no role', function () {
    $user = User::factory()->create(['role_id' => null]);

    expect($user->isAdmin())->toBeFalse();
});
