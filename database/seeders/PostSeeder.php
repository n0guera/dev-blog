<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostStatus;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'test@example.com')->first();
        $publishedStatus = PostStatus::where('name', 'published')->first();

        $tags = collect([
            'laravel', 'vue', 'php', 'javascript', 'api', 'blade',
            'inertia', 'testing', 'database', 'authentication',
        ])->mapWithKeys(fn ($name) => [$name => Tag::firstOrCreate(
            ['slug' => $name],
            ['name' => ucfirst($name)],
        )->id])->toArray();

        $posts = [
            [
                'title' => 'Understanding Rate Limiting in Laravel',
                'slug' => 'understanding-rate-limiting-laravel',
                'content' => <<<'MARKDOWN'
## What is Rate Limiting?

Rate limiting is a technique used to control the number of requests a client can make to your application within a given time window. Think of it as a bouncer at a club — it decides who gets in and who has to wait. Without rate limiting, your API endpoints are vulnerable to abuse, denial-of-service attacks, and resource exhaustion.

In Laravel, rate limiting is built into the HTTP kernel and works through **middleware** and **throttle** configurations. The framework ships with a robust rate limiter that you can customize per-route or globally.

## How Laravel Implements It

Laravel's rate limiter is configured in `AppServiceProvider` using the `RateLimiter` facade:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by(
        $request->user()?->id ?: $request->ip()
    );
});
```

The `perMinute(60)` call creates a sliding window of 60 requests per minute. The `by()` method determines the **rate limit key** — usually the authenticated user's ID or the IP address for guests.

## Applying Rate Limiting to Routes

You can apply rate limiting to routes using the `throttle` middleware:

```php
Route::middleware(['throttle:api'])->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
});
```

You can also define **multiple rate limiters** for different scenarios — for example, stricter limits for password reset endpoints (5 attempts per minute) and relaxed limits for public read endpoints (100 per minute).

## Why It Matters

Without rate limiting, a single malicious actor could flood your database with thousands of requests per second, exhausting memory, CPU, and database connections. Rate limiting protects your infrastructure and ensures fair usage across all users.

> **Tip:** Always rate-limit authentication-related endpoints (login, registration, password reset) more aggressively than public read endpoints.

The framework automatically responds with a `429 Too Many Requests` status code when the limit is exceeded, and the `Retry-After` header tells the client when to retry.
MARKDOWN,
                'excerpt' => 'Learn how Laravel\'s rate limiter protects your API endpoints and how to configure per-route throttling.',
                'tags' => ['laravel', 'api', 'authentication'],
            ],
            [
                'title' => 'Vue 3 Composition API: Why It Changed Everything',
                'slug' => 'vue-3-composition-api-changed-everything',
                'content' => <<<'MARKDOWN'
## The Problem with Options API

Vue 2's Options API organized code by **option type** — data, methods, computed, watchers. This worked fine for small components, but as components grew, related logic became scattered across multiple sections. A single feature's code might be split between `data()`, `methods`, `computed`, and `mounted`, making it hard to follow.

The Composition API solves this by organizing code **by logical concern** rather than by option type. All logic related to a feature lives together, making components easier to read, test, and refactor.

## How It Works

In the Composition API, you use `<script setup>` and the `ref` / `reactive` functions:

```vue
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const count = ref(0)
const doubled = computed(() => count.value * 2)

function increment() {
  count.value++
}

onMounted(() => {
  console.log('Component mounted with count:', count.value)
})
</script>
```

Notice how all the state, computed properties, methods, and lifecycle hooks are grouped together logically. The `<script setup>` syntax also eliminates the need for explicit `export default` and `setup()` function.

## Composables: Reusing Logic

The real power of the Composition API is **composables** — functions that encapsulate and reuse stateful logic:

```ts
// useCounter.ts
import { ref } from 'vue'

export function useCounter(initial = 0) {
  const count = ref(initial)
  const increment = () => count.value++
  const decrement = () => count.value--
  return { count, increment, decrement }
}
```

Any component can now reuse this logic without duplicating code. This is similar to mixins in Vue 2, but composables are **explicit**, **type-safe**, and avoid name collisions.

## TypeScript Integration

The Composition API works naturally with TypeScript. Since you're using plain functions and variables instead of options objects, type inference works out of the box:

```ts
const count = ref<number>(0)  // Ref<number>
const doubled = computed(() => count.value * 2)  // ComputedRef<number>
```

This makes Vue 3 a first-class citizen in the TypeScript ecosystem, something Vue 2 never achieved cleanly.
MARKDOWN,
                'excerpt' => 'A deep dive into Vue 3\'s Composition API, composables, and why it\'s the preferred pattern for modern Vue development.',
                'tags' => ['vue', 'javascript'],
            ],
            [
                'title' => 'Eloquent Relationships: Beyond the Basics',
                'slug' => 'eloquent-relationships-beyond-basics',
                'content' => <<<'MARKDOWN'
## Why Relationships Matter

Laravel's Eloquent ORM is famous for its expressive syntax, and relationships are at the heart of it. Instead of writing raw SQL JOINs, you define relationships as methods on your models. But most developers only scratch the surface — using `hasMany` and `belongsTo` without exploring the full power of Eloquent's relational system.

## Polymorphic Relationships

Polymorphic relationships let a model belong to more than one other model on a single association. For example, a `Vote` model might belong to both a `Post` and a `Comment`:

```php
// Vote model
public function votable(): MorphTo
{
    return $this->morphTo();
}

// Post model
public function votes(): MorphMany
{
    return $this->morphMany(Vote::class, 'votable');
}
```

The `votable_type` and `votable_id` columns on the `votes` table store the class name and ID of the related model. This eliminates the need for separate vote tables per content type.

## Eager Loading and N+1 Problems

The **N+1 query problem** occurs when you load a collection and then access relationships on each item — triggering a separate query per item. Eager loading solves this:

```php
// BAD: N+1 queries
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->user->name;  // Each iteration runs a query
}

// GOOD: 2 queries total
$posts = Post::with('user')->get();
```

You can also eager load **nested relationships** and apply constraints:

```php
$posts = Post::with(['comments' => function ($query) {
    $query->where('approved', true)
          ->with('user')
          ->latest();
}])->get();
```

## Accessors and Mutators

Accessors transform attribute values when you retrieve them, while mutators transform them when you set them. Laravel 11 introduced the `Attribute` cast syntax:

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function fullName(): Attribute
{
    return get: fn () => "{$this->first_name} {$this->last_name}";
}
```

Now `$user->full_name` returns the computed value without storing it in the database. This keeps your data clean while providing convenient, formatted access to your models.
MARKDOWN,
                'excerpt' => 'Master polymorphic relationships, eager loading, and accessors in Laravel Eloquent to build performant, maintainable applications.',
                'tags' => ['laravel', 'php', 'database'],
            ],
            [
                'title' => 'Building Type-Safe APIs with Inertia.js and Wayfinder',
                'slug' => 'type-safe-apis-inertia-wayfinder',
                'content' => <<<'MARKDOWN'
## The SPA Without the API Layer

Inertia.js lets you build single-page applications without writing a separate API. Your Laravel controllers return Inertia responses that render Vue components directly — no JSON serialization, no Axios calls, no CORS configuration. It bridges the gap between server-side routing and client-side rendering.

But there's a gap: how do you ensure the route names you use in Vue components actually match your Laravel routes? One typo and you get a silent 404.

## Enter Wayfinder

Wayfinder generates TypeScript route definitions from your Laravel routes. Instead of hardcoding URL strings in your Vue components, you use type-safe route functions:

```ts
// Without Wayfinder (error-prone)
router.get(`/posts/${slug}`)

// With Wayfinder (type-safe)
import { posts } from '@/actions/App/Http/Controllers/PostController'
router.get(posts.show({ post: slug }))
```

If the route doesn't exist or the parameter name changes, TypeScript catches the error at build time — not in production.

## How Inertia Handles Shared Data

One of Inertia's most powerful features is **shared data**. Middleware can attach data that's available on every page:

```php
// HandleInertiaRequests.php
public function share(Request $request): array
{
    return [
        'auth' => ['user' => $request->user()],
        'flash' => ['success' => session('success')],
    ];
}
```

In Vue, this data is accessible via `usePage().props`:

```ts
const page = usePage()
const user = computed(() => page.props.auth.user)
```

This eliminates the need for separate API calls to fetch authentication state or flash messages on every page.

## The Best of Both Worlds

The combination of Inertia + Wayfinder gives you the developer experience of a traditional server-rendered app with the UX of an SPA. Your routes are defined once in Laravel, and both the server and client stay in sync — automatically.

> **Key insight:** Inertia is not a framework. It's a protocol that connects your server-side and client-side frameworks through a shared contract. Your controllers remain pure Laravel, and your Vue components remain pure Vue.
MARKDOWN,
                'excerpt' => 'How Inertia.js and Wayfinder work together to deliver type-safe, server-driven SPAs without the API boilerplate.',
                'tags' => ['laravel', 'vue', 'inertia', 'api'],
            ],
            [
                'title' => 'Testing Laravel Applications with Pest',
                'slug' => 'testing-laravel-applications-pest',
                'content' => <<<'MARKDOWN'
## Why Testing Matters

Testing isn't optional — it's a safety net that lets you refactor with confidence. A well-tested application catches regressions before your users do. Laravel makes testing first-class, and Pest is the modern testing framework that makes it enjoyable.

Pest is built on top of PHPUnit but offers a cleaner, more expressive syntax. Instead of class-based test cases, you use closure-based `it()` and `test()` functions that read like natural language.

## Your First Test

Here's a basic feature test using Pest and Laravel's testing helpers:

```php
it('can display published posts on the homepage', function () {
    Post::factory()->published()->count(3)->create();

    $response = $this->get('/');

    $response->assertOk()
             ->assertInertiaComponent('Welcome')
             ->assertInertiaProp('latestPosts', fn ($posts) => $posts->count() === 3);
});
```

The `assertInertiaComponent` and `assertInertiaProp` methods verify that the correct Vue component is rendered and receives the expected props. This is possible because of Inertia's testing integration.

## Testing Relationships and Authorization

You can test that policies work correctly:

```php
it('prevents non-owners from deleting posts', function () {
    $post = Post::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
         ->deleteJson("/admin/posts/{$post->slug}")
         ->assertForbidden();
});
```

And test that eager loading prevents N+1 queries:

```php
it('eager loads relationships to prevent N+1', function () {
    Post::factory()->count(5)->create();

    $this->withoutExceptionHandling()
         ->get('/posts')
         ->assertOk();

    $this->assertDatabaseCount('posts', 5);
});
```

## Datasets for Cleaner Tests

Pest's **datasets** feature eliminates duplication when testing multiple scenarios:

```php
it('validates required fields', function (string $field, string $value) {
    Post::factory()->create([$field => $value]);

    $this->postJson('/admin/posts', array_merge(
        Post::factory()->raw(),
        [$field => '']
    ))->assertUnprocessable()
      ->assertJsonValidationErrors($field);
})->with([
    'title' => ['title', ''],
    'content' => ['content', ''],
]);
```

## The Testing Pyramid

Aim for a mix of **unit tests** (fast, isolated), **feature tests** (HTTP-focused), and **browser tests** (full interaction). Laravel and Pest give you all the tools — the key is to actually use them.

> **Remember:** The best test suite is the one you actually write and maintain. Start small, test the critical paths, and grow from there.
MARKDOWN,
                'excerpt' => 'A practical guide to testing Laravel applications with Pest — from basic assertions to Inertia integration and authorization testing.',
                'tags' => ['laravel', 'php', 'testing', 'inertia'],
            ],
        ];

        foreach ($posts as $i => $postData) {
            $author = $i % 2 === 0 ? $admin : $user;

            $post = Post::create([
                'user_id' => $author->id,
                'status_id' => $publishedStatus->id,
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'content' => $postData['content'],
                'excerpt' => $postData['excerpt'],
                'published_at' => now()->subDays(count($posts) - $i),
            ]);

            $tagIds = collect($postData['tags'])
                ->map(fn ($name) => $tags[$name] ?? null)
                ->filter()
                ->values()
                ->all();

            $post->tags()->attach($tagIds);
        }
    }
}
