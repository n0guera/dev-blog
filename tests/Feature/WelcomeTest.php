<?php

use App\Models\Post;
use App\Models\PostStatus;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('welcome page serves shared props from the database cache as plain arrays', function () {
    config(['cache.default' => 'database']);

    $published = PostStatus::factory()->published()->create();
    $draft = PostStatus::factory()->draft()->create();

    $tag = Tag::factory()->create();

    $posts = Post::factory()->count(3)->create(['status_id' => $published->id]);
    $posts->first()->tags()->attach($tag);

    $draftPost = Post::factory()->create(['status_id' => $draft->id]);

    $this->get('/')->assertOk();

    $response = $this->get('/');
    $response->assertOk();

    preg_match('/data-page="([^"]+)"/', $response->getContent(), $matches);
    $page = json_decode(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5));

    expect($page->props->latestPosts)->toBeArray()->toHaveCount(3)
        ->and($page->props->tags)->toBeArray();

    $latestTitles = array_map(fn ($post) => $post->title, $page->props->latestPosts);
    $latestSlugs = array_map(fn ($post) => $post->slug, $page->props->latestPosts);

    foreach ($posts as $post) {
        expect($latestTitles)->toContain($post->title)
            ->and($latestSlugs)->toContain($post->slug);
    }

    expect($latestTitles)->not->toContain($draftPost->title)
        ->and($latestSlugs)->not->toContain($draftPost->slug);
});
