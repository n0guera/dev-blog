# DevBlog

A developer blog built with Laravel 13 and Vue 3. Features an admin panel for publishing posts and a public blog area for visitors. Users can comment on posts and upvote/downvote both posts and comments.

## Tech Stack

- **Backend**: Laravel 13, PHP 8.3+
- **Frontend**: Vue 3, TypeScript, Tailwind CSS 4
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
# Development
php artisan serve
npm run dev
```

## Dependencies

### Composer

- spatie/laravel-sluggable: ^4.0

### NPM

- marked: ^15.0
- dompurify: ^3.2
- @types/dompurify: ^3.0

## File Structure

```
app/Models/
├── Role.php
├── PostStatus.php
├── VoteType.php
├── Post.php
├── Tag.php
├── Comment.php
└── Vote.php

app/Http/Controllers/
├── PostController.php
├── TagController.php
├── CommentController.php
├── VoteController.php
└── Admin/...

app/Policies/
├── PostPolicy.php
├── CommentPolicy.php
├── TagPolicy.php
└── VotePolicy.php

app/Http/Middleware/
└── CheckRole.php

resources/js/pages/
├── posts/
│   ├── Index.vue
│   ├── Show.vue
│   └── Search.vue
└── admin/
    ├── posts/
    └── tags/

resources/js/components/
├── MarkdownEditor.vue
├── PostCard.vue
├── VoteButton.vue
├── CommentItem.vue
├── CommentForm.vue
├── TagInput.vue
└── TagPill.vue
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

### PostController.php

- `index()` - Public: list published posts
- `show(Post $post)` - Public: show post (route model binding by slug)
- `store()` - Admin: create post
- `update(Post $post)` - Admin: update post
- `destroy(Post $post)` - Admin: delete post
- `uploadImage()` - Admin: upload image

### TagController.php

- `index()` - Public: list tags
- `store()` - Admin: create tag
- `update(Tag $tag)` - Admin: update tag
- `destroy(Tag $tag)` - Admin: delete tag

### CommentController.php

- `index(Post $post)` - Public: get comments
- `store(Post $post)` - Auth: create comment
- `update(Comment $comment)` - Owner: update comment
- `destroy(Comment $comment)` - Owner/Admin: delete comment

### VoteController.php

- `upvote($type, $id)` - Auth
- `downvote($type, $id)` - Auth
- `removeVote($type, $id)` - Auth

## Middleware

### CheckRole.php

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin only routes
});
```

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
// Public
Route::get('/', 'Welcome@index')->name('home');
Route::get('/posts', 'PostController@index')->name('posts.index');
Route::get('/posts/{slug}', 'PostController@show')->name('posts.show');
Route::get('/tags', 'TagController@index')->name('tags.index');
Route::get('/tags/{slug}', 'PostController@tagged')->name('posts.byTag');
Route::get('/search', 'PostController@search')->name('posts.search');

// Auth
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', 'Dashboard@index')->name('dashboard');
    Route::post('/comments/{post}', 'CommentController@store')->name('comments.store');
    Route::put('/comments/{comment}', 'CommentController@update')->name('comments.update');
    Route::delete('/comments/{comment}', 'CommentController@destroy')->name('comments.destroy');
    Route::post('/votes/{type}/{id}/up', 'VoteController@upvote')->name('votes.up');
    Route::post('/votes/{type}/{id}/down', 'VoteController@downvote')->name('votes.down');
    Route::delete('/votes/{type}/{id}', 'VoteController@removeVote')->name('votes.remove');
});
```

### admin.php (Admin Only)

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', 'Admin\Dashboard@index')->name('admin.dashboard');
    Route::resource('posts', 'Admin\PostController')->except(['show']);
    Route::post('/posts/{post}/publish', 'Admin\PostController@publish')->name('admin.posts.publish');
    Route::post('/posts/{post}/upload-image', 'Admin\PostController@uploadImage')->name('admin.posts.uploadImage');
    Route::resource('tags', 'Admin\TagController');
});
```

## Vue Pages

### Public Pages

```
resources/js/pages/
├── Welcome.vue
├── posts/
│   ├── Index.vue
│   ├── Show.vue
│   └── Search.vue
└── Profile.vue
```

### Admin Pages

```
resources/js/pages/admin/
├── Dashboard.vue
├── posts/
│   ├── Index.vue
│   ├── Create.vue
│   └── [id]/Edit.vue
└── tags/
    ├── Index.vue
    └── Create.vue
```

### Auth Pages (existing)

- Login.vue, Register.vue, ForgotPassword.vue, ResetPassword.vue, VerifyEmail.vue, TwoFactorChallenge.vue, ConfirmPassword.vue

## Vue Components

- **MarkdownEditor.vue**: Split-view markdown editor with live preview
- **PostCard.vue**: Card for displaying posts in lists
- **PostList.vue**: Wrapper with pagination
- **CommentItem.vue**: Recursive comment display with nested replies
- **CommentForm.vue**: Form for submitting comments
- **CommentsSection.vue**: Wrapper for comments
- **VoteButton.vue**: Upvote/downvote component
- **TagPill.vue**: Visual tag representation
- **TagInput.vue**: Input with tag autocomplete

## Feature Requirements

### Visitor

- View published posts
- View post details
- View comments and replies
- View vote counts
- View tags, search by tag

### User

- All visitor features
- Comment on posts
- Edit/delete own comments
- Upvote/downvote posts
- Upvote/downvote comments

### Admin

- All user features
- Create/edit/delete posts
- Upload featured images
- Publish/draft posts
- Manage tags (CRUD)
- Delete any comment

## Security

1. Use Policies for all CRUD operations
2. Include CSRF token in all forms
3. Use DOMPurify for Markdown sanitization
4. Validate file uploads (images only, max 2MB)
5. Store images in storage/app/public/posts/
6. Apply rate limiting to comments and votes
7. Use Fortify's built-in password hashing

## Markdown Configuration

```javascript
import { marked } from 'marked';
import DOMPurify from 'dompurify';

marked.setOptions({
    breaks: true,
    gfm: true,
    headerIds: true,
    mangle: false,
});

function parseMarkdown(content) {
    const html = marked.parse(content);
    return DOMPurify.sanitize(html);
}
```

## Image Storage

- Directory: storage/app/public/posts/
- Access: /storage/posts/filename.jpg
- Validation: jpeg,png,jpg,gif,webp (max 2MB, 200-1200px width)

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

### Phase 2: Authentication & Authorization ✅ COMPLETE

- [x] Create CheckRole middleware
- [x] Create PostPolicy, CommentPolicy, TagPolicy, VotePolicy

### Phase 3: Backend Controllers

- [ ] Create PostController with CRUD + image upload
- [ ] Create TagController with CRUD
- [ ] Create CommentController
- [ ] Create VoteController (upvote/downvote/remove)
- [ ] Define routes in web.php and admin.php

### Phase 4: Frontend Components

- [ ] Create MarkdownEditor.vue with split view
- [ ] Create TagInput.vue with autocomplete
- [ ] Create TagPill.vue
- [ ] Create PostCard.vue
- [ ] Create PostList.vue
- [ ] Create VoteButton.vue
- [ ] Create CommentItem.vue, CommentForm.vue, CommentsSection.vue

### Phase 5: Frontend Pages

- [ ] Create posts/Index.vue (public listing)
- [ ] Create posts/Show.vue (post detail)
- [ ] Create posts/Search.vue
- [ ] Update Welcome.vue
- [ ] Create admin/Dashboard.vue
- [ ] Create admin/posts/Index.vue, Create.vue, Edit.vue
- [ ] Create admin/tags/Index.vue, Create.vue

### Phase 6: Security & Polish

- [ ] Apply rate limiting to comments and votes routes
- [ ] Verify all Policies are enforced
- [ ] Configure DOMPurify for Markdown sanitization
- [ ] Run tests

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
- [x] English language (en-US)
- [x] Split-view markdown editor
- [x] Vote types stored in database table (vote_types) for scalability
- [x] Separation of concerns: PostController (public) vs Admin\PostController (admin)
- [x] PostResource for consistent API response formatting
- [x] Authorization handled in Policies, not in FormRequests
- [x] Route Model Binding for tags using slug

## What's Implemented

- Database migrations for all entities
- Eloquent models with relationships
- Soft deletes on posts and comments
- Optimized vote scoring with eager loading
- Database seeders for roles, post statuses, vote types
- Factories for all models
- PostController (public): index, show, tagged, search
- Admin\PostController: CRUD operations with image upload
- PostResource: API resource for post data transformation
- PostRequest: form validation (authorization in Policy)

## What's Missing

- TagController, CommentController, VoteController
- Frontend Vue components and pages
- Admin panel frontend
- Comment system backend
- Vote system backend
- Markdown editor component

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

MIT
