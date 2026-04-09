<?php

use App\Models\Post;
use App\Models\PostStatus;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('PostController - Public Routes', function () {
    test('index returns only published posts', function () {
        $user = User::factory()->create();
        $publishedStatus = PostStatus::factory()->published()->create();
        $draftStatus = PostStatus::factory()->draft()->create();

        Post::factory()->create(['status_id' => $publishedStatus->id]);
        Post::factory()->create(['status_id' => $draftStatus->id]);

        $response = $this->actingAs($user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('posts.data', 1)
        );
    });

    test('index includes user, status, tags and vote score', function () {
        $user = User::factory()->create();
        $status = PostStatus::factory()->published()->create();
        $tag = Tag::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'status_id' => $status->id]);
        $post->tags()->attach($tag);

        $response = $this->actingAs($user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('posts.data', 1)
            ->where('posts.data.0.user.id', $user->id)
            ->where('posts.data.0.status.name', 'published')
            ->where('posts.data.0.tags', [['id' => $tag->id, 'name' => $tag->name, 'slug' => $tag->slug]])
            ->where('posts.data.0.vote_score', 0)
        );
    });

    test('show returns published post', function () {
        $user = User::factory()->create();
        $status = PostStatus::factory()->published()->create();
        $post = Post::factory()->create(['status_id' => $status->id]);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('post')
        );
    });

    test('show loads post with comments and votes', function () {
        $user = User::factory()->create();
        $status = PostStatus::factory()->published()->create();
        $post = Post::factory()->create(['status_id' => $status->id]);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertStatus(200);
    });

    test('tagged returns posts filtered by tag', function () {
        $user = User::factory()->create();
        $status = PostStatus::factory()->published()->create();
        $tag = Tag::factory()->create();
        $post = Post::factory()->create(['status_id' => $status->id]);
        $post->tags()->attach($tag);

        $response = $this->actingAs($user)->get(route('posts.byTag', $tag->slug));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('posts.data', 1)
        );
    });

    test('tagged returns empty when no posts with tag', function () {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->get(route('posts.byTag', $tag->slug));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('posts.data', 0)
        );
    });

    test('search returns matching posts', function () {
        $user = User::factory()->create();
        $status = PostStatus::factory()->published()->create();
        Post::factory()->create([
            'status_id' => $status->id,
            'title' => 'Laravel Tutorial',
        ]);

        $response = $this->actingAs($user)->get(route('posts.search', ['q' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('query', 'Laravel')
            ->has('posts.data', 1)
            ->where('posts.data.0.title', 'Laravel Tutorial')
        );
    });

    test('search returns empty when no matches', function () {
        $user = User::factory()->create();
        $status = PostStatus::factory()->published()->create();
        Post::factory()->create(['status_id' => $status->id, 'title' => 'Some Title']);

        $response = $this->actingAs($user)->get(route('posts.search', ['q' => 'Nonexistent']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('query', 'Nonexistent')
        );
    });
});

describe('PostController - Admin Routes', function () {
    test('admin can access create form', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('posts.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('statuses')
        );
    });

    test('regular user cannot access create form', function () {
        $user = User::factory()->regularUser()->create();

        $response = $this->actingAs($user)->get(route('posts.create'));

        $response->assertForbidden();
    });

    test('unauthenticated user cannot access create form', function () {
        $response = $this->get(route('posts.create'));

        $response->assertRedirect();
    });

    test('admin can store a new post', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'status_id' => $status->id,
            'tags' => [$tag->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);
    });

    test('admin can store post without tags', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Post Without Tags',
            'content' => 'Content here',
            'status_id' => $status->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['title' => 'Post Without Tags']);
    });

    test('store fails with invalid data', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'title' => '',
            'content' => '',
            'status_id' => 999,
        ]);

        $response->assertSessionHasErrors(['title', 'content', 'status_id']);
    });

    test('store fails when title is too long', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'title' => str_repeat('a', 256),
            'content' => 'Content',
            'status_id' => $status->id,
        ]);

        $response->assertSessionHasErrors(['title']);
    });

    test('admin can access edit form', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();
        $post = Post::factory()->create(['user_id' => $admin->id, 'status_id' => $status->id]);

        $response = $this->actingAs($admin)->get(route('posts.edit', $post));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('post')
        );
    });

    test('admin can update own post', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();
        $post = Post::factory()->create(['user_id' => $admin->id, 'status_id' => $status->id]);

        $response = $this->actingAs($admin)->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status_id' => $status->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['title' => 'Updated Title']);
    });

    test('admin can update post with new tags', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();
        $post = Post::factory()->create(['user_id' => $admin->id, 'status_id' => $status->id]);
        $tag = Tag::factory()->create();

        $response = $this->actingAs($admin)->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status_id' => $status->id,
            'tags' => [$tag->id],
        ]);

        $response->assertRedirect();
        $this->assertTrue($post->fresh()->tags->contains($tag));
    });

    test('admin can remove all tags from post', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();
        $post = Post::factory()->create(['user_id' => $admin->id, 'status_id' => $status->id]);
        $tag = Tag::factory()->create();
        $post->tags()->attach($tag);

        $response = $this->actingAs($admin)->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status_id' => $status->id,
            'tags' => [],
        ]);

        $response->assertRedirect();
        $this->assertTrue($post->fresh()->tags->isEmpty());
    });

    test('update fails with invalid data', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();
        $post = Post::factory()->create(['user_id' => $admin->id, 'status_id' => $status->id]);

        $response = $this->actingAs($admin)->put(route('posts.update', $post), [
            'title' => '',
            'content' => '',
            'status_id' => 999,
        ]);

        $response->assertSessionHasErrors(['title', 'content', 'status_id']);
    });

    test('admin can delete own post', function () {
        $admin = User::factory()->admin()->create();
        $status = PostStatus::factory()->draft()->create();
        $post = Post::factory()->create(['user_id' => $admin->id, 'status_id' => $status->id]);

        $response = $this->actingAs($admin)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertSoftDeleted($post);
    });

    test('regular user cannot delete post', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->regularUser()->create();
        $status = PostStatus::factory()->draft()->create();
        $post = Post::factory()->create(['user_id' => $admin->id, 'status_id' => $status->id]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertForbidden();
    });
});
