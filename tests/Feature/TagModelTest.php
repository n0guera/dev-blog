<?php

use App\Models\Post;
use App\Models\PostStatus;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('tag has many posts', function () {
    $tag = Tag::factory()->create();
    $post = Post::factory()->create();

    $post->tags()->attach($tag);

    expect($tag->posts->first()->is($post))->toBeTrue();
});

test('tag post count returns correct value', function () {
    $tag = Tag::factory()->create();
    $status = PostStatus::factory()->create(['name' => 'test-status']);
    $posts = Post::factory()->count(3)->create(['status_id' => $status->id]);

    $posts->each(fn ($post) => $post->tags()->attach($tag));

    expect($tag->post_count)->toBe(3);
});

test('tag post count is zero when no posts', function () {
    $tag = Tag::factory()->create();

    expect($tag->post_count)->toBe(0);
});

test('tag can be created with name and slug', function () {
    $tag = Tag::factory()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    expect($tag->name)->toBe('Laravel');
    expect($tag->slug)->toBe('laravel');
});
