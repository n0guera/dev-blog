# DevBlog

A developer blog built with Laravel 13 and Vue 3. Features an admin panel for publishing posts and a public blog area for visitors. Users can comment on posts and upvote/downvote both posts and comments.

## Tech Stack

- **Backend**: Laravel 13, PHP 8.3+
- **Frontend**: Vue 3, TypeScript, Tailwind CSS 4, Inertia.js
- **Authentication**: Laravel Fortify
- **UI Components**: Shadcn-vue (Reka UI)
- **Database**: SQLite (default), configurable to MySQL/PostgreSQL

## Features

- Public blog with published posts
- Markdown editor with live preview
- Comment system with nested replies
- Upvote/downvote system for posts and comments
- Tag-based post organization
- User roles: Visitor, User, Admin
- User settings (profile, password, 2FA, appearance)

## Installation

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create database
touch database/database.sqlite
php artisan migrate

# Link storage
php artisan storage:link
```

## Running

```bash
# Development (all-in-one)
composer dev

# Or run separately
php artisan serve
npm run dev
```

## Dependencies

### Composer

- spatie/laravel-sluggable: ^3.8
- inertiajs/inertia-laravel: ^2.0
- laravel/fortify: ^1.34
- laravel/wayfinder: ^0.1.14

### NPM

- @inertiajs/vue3: ^2.3.7
- marked: ^18.0.0
- dompurify: ^3.4.0
- reka-ui: ^2.6.1
- lucide-vue-next: ^0.468.0
- tailwindcss: ^4.1.1
- vue: ^3.5.13

## File Structure

```
app/
├── Models/
│   ├── Role.php
│   ├── PostStatus.php
│   ├── VoteType.php
│   ├── Post.php
│   ├── Tag.php
│   ├── Comment.php
│   └── Vote.php
├── Http/
│   ├── Controllers/
│   │   ├── PostController.php
│   │   ├── TagController.php
│   │   ├── CommentController.php
│   │   ├── VoteController.php
│   │   ├── Settings/
│   │   │   ├── ProfileController.php
│   │   │   └── SecurityController.php
│   │   └── Admin/
│   │       ├── PostController.php
│   │       └── TagController.php
│   ├── Requests/
│   │   ├── PostRequest.php
│   │   ├── TagRequest.php
│   │   ├── CommentRequest.php
│   │   └── Settings/
│   │       ├── ProfileUpdateRequest.php
│   │       ├── ProfileDeleteRequest.php
│   │       ├── PasswordUpdateRequest.php
│   │       └── TwoFactorAuthenticationRequest.php
│   ├── Resources/
│   │   └── PostResource.php
│   └── Middleware/
│       ├── CheckRole.php
│       ├── HandleInertiaRequests.php
│       └── HandleAppearance.php
├── Policies/
│   ├── PostPolicy.php
│   ├── CommentPolicy.php
│   ├── TagPolicy.php
│   └── VotePolicy.php
└── Providers/
    ├── AppServiceProvider.php
    └── FortifyServiceProvider.php

resources/js/
├── pages/
│   ├── Welcome.vue                  (boilerplate - needs implementation)
│   ├── Dashboard.vue                ✅
│   ├── auth/
│   │   ├── Login.vue                ✅
│   │   ├── Register.vue             ✅
│   │   ├── ForgotPassword.vue       ✅
│   │   ├── ResetPassword.vue        ✅
│   │   ├── VerifyEmail.vue          ✅
│   │   ├── TwoFactorChallenge.vue   ✅
│   │   └── ConfirmPassword.vue      ✅
│   ├── posts/
│   │   ├── Index.vue                ✅
│   │   ├── Show.vue                 ✅
│   │   └── Search.vue              (stub - needs implementation)
│   ├── settings/
│   │   ├── Profile.vue              ✅
│   │   ├── Security.vue             ✅
│   │   └── Appearance.vue           ✅
│   └── admin/
│       ├── posts/
│       │   ├── Index.vue            ✅
│       │   ├── Create.vue           ✅
│       │   └── Edit.vue            (stub - needs implementation)
│       └── tags/
│           ├── Index.vue            ❌ MISSING
│           └── Create.vue           ❌ MISSING
├── components/
│   ├── MarkdownEditor.vue           ✅ (67 lines, split-view editor)
│   ├── MarkdownRenderer.vue         ✅
│   ├── PostForm.vue                 ✅ (55 lines, form with status/tags)
│   ├── PostCard.vue                 ❌ EMPTY (0 lines)
│   ├── PostList.vue                 ❌ EMPTY (0 lines)
│   ├── VoteButton.vue               ❌ EMPTY (0 lines)
│   ├── CommentItem.vue              ❌ EMPTY (0 lines)
│   ├── CommentForm.vue              ❌ EMPTY (0 lines)
│   ├── CommentsSection.vue          ❌ EMPTY (0 lines)
│   ├── TagPill.vue                  ❌ EMPTY (0 lines)
│   └── TagInput.vue                 ❌ EMPTY (0 lines)
└── layouts/
    ├── AppLayout.vue                ✅
    ├── AppHeaderLayout.vue          ✅
    ├── AppSidebarLayout.vue         ✅
    ├── AuthLayout.vue               ✅
    └── settings/
        └── Layout.vue               ✅
```

## Database Schema

### roles

```php
$table->id();
$table->string('name')->unique();  // 'user', 'admin'
$table->text('description')->nullable();
$table->timestamps();
```

### post_statuses

```php
$table->id();
$table->string('name')->unique();  // 'draft', 'published'
$table->timestamps();
```

### vote_types

```php
$table->id();
$table->string('name')->unique();  // 'up', 'down'
$table->timestamps();
```

### users

Add to existing Fortify table:

```php
$table->foreignId('role_id')->constrained()->default(1);
```

### posts

```php
$table->id();
$table->foreignId('user_id')->constrained()->onDelete('cascade');
$table->foreignId('status_id')->constrained('post_statuses')->default(1);
$table->string('title');
$table->string('slug')->unique();
$table->text('content');
$table->text('excerpt')->nullable();
$table->string('featured_image')->nullable();
$table->timestamp('published_at')->nullable();
$table->timestamps();
```

### tags

```php
$table->id();
$table->string('name')->unique();
$table->string('slug')->unique();
$table->timestamps();
```

### post_tag (pivot)

```php
$table->foreignId('post_id')->constrained()->onDelete('cascade');
$table->foreignId('tag_id')->constrained()->onDelete('cascade');
$table->primary(['post_id', 'tag_id']);
```

### comments

```php
$table->id();
$table->foreignId('post_id')->constrained()->onDelete('cascade');
$table->foreignId('user_id')->constrained();
$table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
$table->text('content');
$table->timestamps();
```

### votes

```php
$table->id();
$table->morphs('votable');
$table->foreignId('user_id')->constrained();
$table->foreignId('vote_type_id')->constrained('vote_types');
$table->unique(['votable_type', 'votable_id', 'user_id']);
$table->timestamps();
```

## Models

### Role.php

```php
protected $fillable = ['name', 'description'];
public function users(): HasMany
```

### PostStatus.php

```php
protected $fillable = ['name'];
public function posts(): HasMany
```

### VoteType.php

```php
protected $fillable = ['name'];
public function votes(): HasMany
```

### User.php (add to existing)

```php
public function role(): BelongsTo
public function posts(): HasMany
public function comments(): HasMany
public function votes(): HasMany
public function isAdmin(): bool
```

### Post.php

```php
use HasFactory, SoftDeletes;
protected $fillable = ['title', 'slug', 'content', 'excerpt', 'featured_image', 'status_id', 'published_at', 'user_id'];

public function user(): BelongsTo
public function status(): BelongsTo
public function tags(): BelongsToMany
public function comments(): HasMany
public function votes(): MorphMany
public function getVoteScoreAttribute(): int
```

### Tag.php

```php
use HasFactory;
protected $fillable = ['name', 'slug'];
public function posts(): BelongsToMany
public function getPostCountAttribute(): int
```

### Comment.php

```php
use HasFactory, SoftDeletes;
protected $fillable = ['content', 'post_id', 'user_id', 'parent_id'];

public function post(): BelongsTo
public function user(): BelongsTo
public function parent(): BelongsTo
public function replies(): HasMany
public function votes(): MorphMany
public function getVoteScoreAttribute(): int
```

### Vote.php

```php
use HasFactory;
protected $fillable = ['user_id', 'votable_type', 'votable_id', 'vote_type_id'];

public function user(): BelongsTo
public function votable(): MorphTo
public function voteType(): BelongsTo
```

## Controllers

### PostController.php (public)

- `index()` - List published posts with pagination
- `show(Post $post)` - Show post with comments
- `tagged(Tag $tag)` - List posts by tag
- `search(Request $request)` - Search posts by title/content

### Admin\PostController.php

- `index()` - List all posts (admin)
- `create()` - Show create form
- `store(PostRequest)` - Create post
- `edit(Post $post)` - Show edit form
- `update(PostRequest, Post $post)` - Update post
- `destroy(Post $post)` - Delete post
- `uploadImage(Request)` - Upload featured image

### TagController.php (public)

- `index()` - List tags

### Admin\TagController.php

- `index()` - List all tags with post counts
- `create()` - Show create form
- `store(TagRequest)` - Create tag
- `edit(Tag $tag)` - Show edit form
- `update(TagRequest, Tag $tag)` - Update tag
- `destroy(Tag $tag)` - Delete tag

### CommentController.php

- `index(Post $post)` - Get comments for post
- `store(Post $post)` - Create comment
- `update(Comment $comment)` - Update comment
- `destroy(Comment $comment)` - Delete comment

### VoteController.php

- `upvote($type, $id)` - Upvote or toggle
- `downvote($type, $id)` - Downvote or toggle
- `removeVote($type, $id)` - Remove vote

### Settings Controllers

- `ProfileController` - Update profile, delete account
- `SecurityController` - Update password, toggle 2FA

## Policies

### PostPolicy

- viewAny: Public
- view: Public
- create: Auth
- update: Owner
- delete: Admin

### CommentPolicy

- viewAny: Public
- view: Public
- create: Auth
- update: Owner
- delete: Owner or Admin

### TagPolicy

- viewAny: Public
- view: Public
- create: Admin
- update: Admin
- delete: Admin

### VotePolicy

- create: Auth
- delete: Owner

## Routes

### web.php (Public & Auth)

```php
Route::inertia('/', 'Welcome')->name('home');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag:slug}', [PostController::class, 'tagged'])->name('posts.byTag');
Route::get('/search', [PostController::class, 'search'])->name('posts.search');
Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('comments.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/votes/{type}/{id}/up', [VoteController::class, 'upvote'])->name('votes.up');
    Route::post('/votes/{type}/{id}/down', [VoteController::class, 'downvote'])->name('votes.down');
    Route::delete('/votes/{type}/{id}', [VoteController::class, 'removeVote'])->name('votes.remove');
});
```

### admin.php (Admin Only)

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/upload-image', [PostController::class, 'uploadImage'])->name('posts.uploadImage');

    Route::resource('tags', TagController::class)->names([...]);
});
```

## Security

1. Use Policies for all CRUD operations
2. Include CSRF token in all forms
3. Use DOMPurify for Markdown sanitization
4. Validate file uploads (images only, max 2MB)
5. Store images in storage/app/public/posts/
6. Apply rate limiting to comments and votes
7. Use Fortify's built-in password hashing

## Commands

```bash
# Linting
composer lint
npm run lint

# Format
npm run format

# Type checking
npm run types:check

# Tests
php artisan test

# Database
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed
php artisan storage:link
```

## User Roles

| Role    | Permissions                         |
| ------- | ----------------------------------- |
| Visitor | View posts, comments, vote counts   |
| User    | All visitor + comment + vote        |
| Admin   | All user + CRUD posts + manage tags |

## Project Phases

### Phase 1: Database & Models ✅ COMPLETE

- [x] Create migrations: roles, post_statuses, vote_types, posts, tags, post_tag, comments, votes
- [x] Update users table with role_id foreign key
- [x] Create models: Role, PostStatus, VoteType, Post, Tag, Comment, Vote
- [x] Add relationships to User model
- [x] Create database seeders for roles and default data
- [x] Create factories for all models

### Phase 2: Authentication & Authorization ✅ COMPLETE

- [x] Create CheckRole middleware
- [x] Create PostPolicy, CommentPolicy, TagPolicy, VotePolicy
- [x] Fortify auth flows (login, register, password reset, email verification, 2FA)

### Phase 3: Backend Controllers ✅ COMPLETE

- [x] Create PostController (public) with index, show, tagged, search
- [x] Create Admin\PostController with full CRUD + image upload
- [x] Create TagController (public) + Admin\TagController (CRUD)
- [x] Create CommentController (index, store, update, destroy)
- [x] Create VoteController (upvote, downvote, removeVote)
- [x] Create Settings controllers (ProfileController, SecurityController)
- [x] Define routes in web.php, admin.php, settings.php
- [x] Create PostResource, PostRequest, TagRequest, CommentRequest

### Phase 4: Frontend Components ✅ COMPLETE

- [x] Create MarkdownEditor.vue with split view
- [x] Create MarkdownRenderer.vue for post display
- [x] Create PostForm.vue for post create/edit
- [x] Create PostCard.vue — post preview card with title, excerpt, tags, metadata
- [x] Create PostList.vue — paginated post list with prev/next navigation
- [x] Create VoteButton.vue — upvote/downvote with score, auth check, toggle behavior
- [x] Create CommentItem.vue — recursive comment display with replies, vote, reply toggle
- [x] Create CommentForm.vue — textarea + submit with Inertia useForm
- [x] Create CommentsSection.vue — wrapper with comment form + recursive comment list
- [x] Create TagPill.vue — rounded pill badge with optional link
- [x] Create TagInput.vue — autocomplete input with tag selection and removal

### Phase 5: Frontend Pages ✅ COMPLETE

- [x] Create Welcome.vue — hero section + latest posts grid
- [x] Create posts/Index.vue — uses PostList component
- [x] Create posts/Show.vue — full post with VoteButton, Tags, CommentsSection
- [x] Create posts/Search.vue — search input + PostList results
- [x] Create Dashboard.vue — admin links
- [x] Create admin/posts/Index.vue — table with pagination
- [x] Create admin/posts/Create.vue — uses PostForm
- [x] Create admin/posts/Edit.vue — uses PostForm with loaded data
- [x] Create admin/tags/Index.vue — table with post counts, edit/delete
- [x] Create admin/tags/Create.vue — name field, slug auto-generated
- [x] Settings pages: Profile.vue, Security.vue, Appearance.vue
- [x] Auth pages: Login, Register, ForgotPassword, ResetPassword, VerifyEmail, TwoFactorChallenge, ConfirmPassword

### Phase 6: Integration & Security

- [x] Wire PostList into posts/Index.vue and posts/Search.vue
- [x] Wire VoteButton, CommentsSection, TagPill into posts/Show.vue
- [x] Wire PostForm into admin/posts/Edit.vue
- [x] Update PostResource to include comments data
- [x] Run full test suite — 130 tests passing
- [ ] Apply rate limiting to comments and votes routes
- [ ] Final visual polish pass

## Architectural Decisions

- [x] Use marked for Markdown parsing
- [x] Use DOMPurify for HTML sanitization
- [x] Use spatie/laravel-sluggable for URL slugs
- [x] Store images locally in storage/app/public/posts/
- [x] One vote per user per post/comment
- [x] Visitors can view but not interact
- [x] Admin has full CRUD on posts and tags
- [x] Owner can edit/delete own comments
- [x] Admin can delete any comment
- [x] Split-view markdown editor
- [x] Vote types stored in database table (vote_types) for scalability
- [x] Separation of concerns: PostController (public) vs Admin\PostController (admin)
- [x] PostResource for consistent API response formatting
- [x] Authorization handled in Policies, not in FormRequests
- [x] Inertia.js for SPA-like experience without API layer
- [x] Wayfinder for type-safe route generation
- [x] Shadcn-vue (Reka UI) for UI component library

## What's Implemented (Verified)

### Backend — 100% ✅
- All migrations, models, relationships, seeders, factories
- All controllers (public, admin, settings)
- All policies and middleware
- Form request validation
- API resource transformation (PostResource with comments, tags, votes)

### Frontend — 100% ✅
- **11/11 components** functional
- **15/15 pages** functional
- All pages use AppLayout with consistent header/footer
- Dark mode support throughout

### Tests — 130 Tests Passing ✅
- Feature tests for all models and controllers
- Auth flow tests
- Settings tests
- Unit tests for model relationships

## Performance Optimizations

### Post::getVoteScoreAttribute()

Uses Laravel's `withCount()` with conditional fallback to prevent N+1 queries:

```php
public function scopeWithVotes($query): \Illuminate\Database\Eloquent\Builder
{
    return $query->withCount(['upVotes', 'downVotes']);
}

public function getVoteScoreAttribute(): int
{
    if ($this->upVotes_count !== null && $this->downVotes_count !== null) {
        return (int) $this->upVotes_count - (int) $this->downVotes_count;
    }
    return (int) $this->upVotes()->count() - (int) $this->downVotes()->count();
}
```

Usage: `Post::withVotes()->get()`

### Tag::getPostCountAttribute()

Uses `relationLoaded()` to check if eager loaded, otherwise falls back to count:

```php
public function getPostCountAttribute(): int
{
    if ($this->relationLoaded('posts')) {
        return $this->posts->count();
    }
    return $this->posts()->count();
}
```

Usage: `Tag::withPostCount()->get()`

## License

All rights reserved.
