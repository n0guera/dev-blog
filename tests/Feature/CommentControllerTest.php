<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CommentController', function () {
    describe('index', function () {
        test('returns comments for a post', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

            $response = $this->getJson(route('comments.index', $post));

            $response->assertStatus(200);
            $response->assertJsonStructure(['comments']);
            $response->assertJsonCount(1, 'comments');
            $response->assertJsonPath('comments.0.id', $comment->id);
        });

        test('returns only root comments (no replies)', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $parentComment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);
            $reply = Comment::factory()->create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'parent_id' => $parentComment->id,
            ]);

            $response = $this->getJson(route('comments.index', $post));

            $response->assertStatus(200);
            $response->assertJsonCount(1, 'comments');
            $response->assertJsonPath('comments.0.id', $parentComment->id);
        });

        test('includes user and votes with comments', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

            $response = $this->getJson(route('comments.index', $post));

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'comments' => [
                    '*' => ['id', 'content', 'user', 'votes', 'replies'],
                ],
            ]);
        });
    });

    describe('store', function () {
        test('authenticated user can create comment', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->post(route('comments.store', $post), [
                'content' => 'This is a test comment.',
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('comments', [
                'post_id' => $post->id,
                'user_id' => $user->id,
                'content' => 'This is a test comment.',
            ]);
        });

        test('authenticated user can create reply to comment', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $parentComment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

            $response = $this->actingAs($user)->post(route('comments.store', $post), [
                'content' => 'This is a reply.',
                'parent_id' => $parentComment->id,
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('comments', [
                'post_id' => $post->id,
                'user_id' => $user->id,
                'parent_id' => $parentComment->id,
                'content' => 'This is a reply.',
            ]);
        });

        test('unauthenticated user cannot create comment', function () {
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->post(route('comments.store', $post), [
                'content' => 'Test comment',
            ]);

            $response->assertRedirect('/login');
        });

        test('store fails with empty content', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->post(route('comments.store', $post), [
                'content' => '',
            ]);

            $response->assertSessionHasErrors('content');
        });

        test('store fails with content exceeding max length', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->post(route('comments.store', $post), [
                'content' => str_repeat('a', 2001),
            ]);

            $response->assertSessionHasErrors('content');
        });

        test('store fails with invalid parent_id', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);

            $response = $this->actingAs($user)->post(route('comments.store', $post), [
                'content' => 'Test reply',
                'parent_id' => 999999,
            ]);

            $response->assertSessionHasErrors('parent_id');
        });
    });

    describe('update', function () {
        test('comment owner can update their comment', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

            $response = $this->actingAs($user)->put(route('comments.update', $comment), [
                'content' => 'Updated comment content.',
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('comments', [
                'id' => $comment->id,
                'content' => 'Updated comment content.',
            ]);
        });

        test('non-owner cannot update comment', function () {
            $user = User::factory()->create();
            $otherUser = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $otherUser->id]);

            $response = $this->actingAs($user)->put(route('comments.update', $comment), [
                'content' => 'Hacked content',
            ]);

            $response->assertForbidden();
        });

        test('unauthenticated user cannot update comment', function () {
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id]);

            $response = $this->put(route('comments.update', $comment), [
                'content' => 'Updated',
            ]);

            $response->assertRedirect('/login');
        });
    });

    describe('destroy', function () {
        test('comment owner can delete their comment', function () {
            $user = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

            $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

            $response->assertRedirect();
            $this->assertSoftDeleted($comment);
        });

        test('admin can delete any comment', function () {
            $user = User::factory()->create();
            $admin = User::factory()->admin()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

            $response = $this->actingAs($admin)->delete(route('comments.destroy', $comment));

            $response->assertRedirect();
            $this->assertSoftDeleted($comment);
        });

        test('regular user cannot delete another user comment', function () {
            $user = User::factory()->create();
            $otherUser = User::factory()->create();
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $otherUser->id]);

            $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

            $response->assertForbidden();
        });

        test('unauthenticated user cannot delete comment', function () {
            $status = PostStatus::factory()->published()->create();
            $post = Post::factory()->create(['status_id' => $status->id]);
            $comment = Comment::factory()->create(['post_id' => $post->id]);

            $response = $this->delete(route('comments.destroy', $comment));

            $response->assertRedirect('/login');
        });
    });
});
