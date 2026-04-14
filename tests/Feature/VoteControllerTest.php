<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostStatus;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoteType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('VoteController', function () {
    beforeEach(function () {
        VoteType::firstOrCreate(['name' => 'up']);
        VoteType::firstOrCreate(['name' => 'down']);
    });

    describe('upvote', function () {
        test('authenticated user can upvote a post', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->postJson(route('votes.up', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $response->assertJsonPath('message', 'Upvoted.');
            $this->assertDatabaseHas('votes', [
                'user_id' => $user->id,
                'votable_type' => Post::class,
                'votable_id' => $post->id,
            ]);
        });

        test('authenticated user can upvote a comment', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id]);

            $response = $this->actingAs($user)->postJson(route('votes.up', ['type' => 'comment', 'id' => $comment->id]));

            $response->assertStatus(200);
            $this->assertDatabaseHas('votes', [
                'user_id' => $user->id,
                'votable_type' => Comment::class,
                'votable_id' => $comment->id,
            ]);
        });

        test('upvote toggles off when clicking again', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $this->actingAs($user)->postJson(route('votes.up', ['type' => 'post', 'id' => $post->id]));

            $response = $this->actingAs($user)->postJson(route('votes.up', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $response->assertJsonPath('message', 'Vote removed.');
            $this->assertDatabaseMissing('votes', [
                'user_id' => $user->id,
                'votable_type' => Post::class,
                'votable_id' => $post->id,
            ]);
        });

        test('upvote changes to downvote when already upvoted', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $upVoteType = VoteType::where('name', 'up')->first();
            $downVoteType = VoteType::where('name', 'down')->first();

            $this->actingAs($user)->postJson(route('votes.up', ['type' => 'post', 'id' => $post->id]));

            $vote = Vote::where('user_id', $user->id)
                ->where('votable_type', Post::class)
                ->where('votable_id', $post->id)
                ->first();
            $this->assertEquals($upVoteType->id, $vote->vote_type_id);

            $response = $this->actingAs($user)->postJson(route('votes.down', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $vote->refresh();
            $this->assertEquals($downVoteType->id, $vote->vote_type_id);
        });

        test('unauthenticated user cannot upvote', function () {
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->postJson(route('votes.up', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(401);
        });

        test('upvote fails with invalid type', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create();

            $response = $this->actingAs($user)->postJson(route('votes.up', ['type' => 'invalid', 'id' => $post->id]));

            $response->assertStatus(500);
        });
    });

    describe('downvote', function () {
        test('authenticated user can downvote a post', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->postJson(route('votes.down', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $response->assertJsonPath('message', 'Downvoted.');
            $this->assertDatabaseHas('votes', [
                'user_id' => $user->id,
                'votable_type' => Post::class,
                'votable_id' => $post->id,
            ]);
        });

        test('downvote toggles off when clicking again', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $this->actingAs($user)->postJson(route('votes.down', ['type' => 'post', 'id' => $post->id]));

            $response = $this->actingAs($user)->postJson(route('votes.down', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $response->assertJsonPath('message', 'Vote removed.');
        });

        test('unauthenticated user cannot downvote', function () {
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->postJson(route('votes.down', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(401);
        });
    });

    describe('removeVote', function () {
        test('authenticated user can remove their vote', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $vote = Vote::factory()->create([
                'user_id' => $user->id,
                'votable_type' => Post::class,
                'votable_id' => $post->id,
            ]);

            $response = $this->actingAs($user)->deleteJson(route('votes.remove', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $response->assertJsonPath('message', 'Vote removed.');
            $this->assertDatabaseMissing('votes', ['id' => $vote->id]);
        });

        test('remove vote works when no vote exists', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->deleteJson(route('votes.remove', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $response->assertJsonPath('message', 'Vote removed.');
        });

        test('unauthenticated user cannot remove vote', function () {
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->deleteJson(route('votes.remove', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(401);
        });
    });

    describe('vote score', function () {
        test('upvote increases post score', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->postJson(route('votes.up', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $response->assertJsonPath('score', 1);
        });

        test('downvote decreases post score', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->postJson(route('votes.down', ['type' => 'post', 'id' => $post->id]));

            $response->assertStatus(200);
            $response->assertJsonPath('score', -1);
        });
    });
});
