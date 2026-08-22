# DevBlog

Developer blog built with Laravel 13 + Vue 3 + Inertia.js. Admin panel for publishing, public area for reading, with comments, votes, and tags.

## Tech Stack

| Layer    | Tech                                            |
| -------- | ----------------------------------------------- |
| Backend  | Laravel 13, PHP 8.3+                            |
| Frontend | Vue 3, TypeScript, Tailwind CSS 4, Inertia.js   |
| Auth     | Laravel Fortify                                 |
| UI       | Shadcn-vue (Reka UI)                            |
| DB       | SQLite (default), MySQL/PostgreSQL configurable |

## Features

- Public blog with Markdown rendering (marked + DOMPurify)
- Split-view Markdown editor with live preview
- Nested comment system with replies
- Upvote/downvote on posts and comments (one per user)
- Tag-based organization with autocomplete
- Roles: Visitor, User, Admin
- User settings: profile, password, 2FA, appearance
- Admin panel: post CRUD, image upload, tag management

## Quick Start

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
php artisan storage:link
composer dev  # runs server + queue + vite concurrently
```

## Commands

```bash
composer dev          # Full dev stack
composer lint         # PHP code style
npm run lint          # ESLint
npm run format        # Prettier
npm run types:check   # TypeScript
php artisan test      # Pest tests
```

## Project Structure

```
app/
├── Models/           Role, PostStatus, VoteType, Post, Tag, Comment, Vote
├── Http/
│   ├── Controllers/  PostController, CommentController, VoteController, TagController
│   │   ├── Admin/    PostController, TagController
│   │   └── Settings/ ProfileController, SecurityController
│   ├── Requests/     PostRequest, TagRequest, CommentRequest + Settings requests
│   ├── Resources/    PostResource
│   └── Middleware/    CheckRole, HandleInertiaRequests, HandleAppearance
├── Policies/         PostPolicy, CommentPolicy, TagPolicy, VotePolicy
└── Providers/        AppServiceProvider, FortifyServiceProvider

resources/js/
├── pages/
│   ├── Welcome, Dashboard
│   ├── posts/        Index, Show, Search
│   ├── admin/posts/  Index, Create, Edit
│   ├── admin/tags/   Index, Create
│   ├── settings/     Profile, Security, Appearance
│   └── auth/         Login, Register, ForgotPassword, ResetPassword, VerifyEmail, TwoFactorChallenge, ConfirmPassword
├── components/
│   ├── MarkdownEditor, MarkdownRenderer, PostForm
│   ├── PostCard, PostList
│   ├── VoteButton
│   ├── CommentForm, CommentItem, CommentsSection
│   └── TagPill, TagInput
└── layouts/          AppLayout, AuthLayout, AppHeaderLayout, AppSidebarLayout, settings/Layout

database/
├── migrations/       roles, post_statuses, vote_types, posts, tags, post_tag, comments, votes
├── factories/        All 8 models
└── seeders/          Roles, PostStatuses, VoteTypes
```

## Database Schema

| Table           | Key Columns                                                                              |
| --------------- | ---------------------------------------------------------------------------------------- |
| `roles`         | name (unique), description                                                               |
| `post_statuses` | name (unique) — draft, published                                                         |
| `vote_types`    | name (unique) — up, down                                                                 |
| `users`         | + role_id FK → roles                                                                     |
| `posts`         | user_id, status_id, title, slug (unique), content, excerpt, featured_image, published_at |
| `tags`          | name (unique), slug (unique)                                                             |
| `post_tag`      | post_id + tag_id (composite PK)                                                          |
| `comments`      | post_id, user_id, parent_id (self-referencing FK), content                               |
| `votes`         | votable_type + votable_id (morph), user_id, vote_type_id. Unique per user+votable.       |

## Models & Relationships

| Model       | Key Relationships                                                                                                 |
| ----------- | ----------------------------------------------------------------------------------------------------------------- |
| **User**    | belongsTo Role, hasMany Posts/Comments/Votes. `isAdmin()` method.                                                 |
| **Post**    | belongsTo User/Status, belongsToMany Tags, hasMany Comments, morphMany Votes. SoftDeletes. Scopes: `withVotes()`. |
| **Tag**     | belongsToMany Posts. Accessor: `post_count`.                                                                      |
| **Comment** | belongsTo Post/User/Parent, hasMany Replies (recursive), morphMany Votes. SoftDeletes.                            |
| **Vote**    | belongsTo User/VoteType, morphTo Votable.                                                                         |

## Controllers

| Controller                      | Methods                                                  |
| ------------------------------- | -------------------------------------------------------- |
| **PostController** (public)     | index, show, tagged, search                              |
| **Admin\PostController**        | index, create, store, edit, update, destroy, uploadImage |
| **TagController** (public)      | index                                                    |
| **Admin\TagController**         | Full CRUD resource                                       |
| **CommentController**           | index, store, update, destroy                            |
| **VoteController**              | upvote, downvote, removeVote (JSON responses)            |
| **Settings/ProfileController**  | update, destroy                                          |
| **Settings/SecurityController** | updatePassword, enable2FA, confirm2FA, disable2FA        |

## Routes

**Public:** `/` (welcome), `/posts`, `/posts/{slug}`, `/tags`, `/tags/{slug}`, `/search`, `/posts/{post}/comments`

**Auth:** `/dashboard`, POST/PUT/DELETE comments + votes

**Admin:** `/admin/posts` (CRUD), `/admin/posts/upload-image`, `/admin/tags` (CRUD resource)

**Settings:** `/settings/profile`, `/settings/password`, `/settings/appearance`

## Policies

| Policy  | viewAny/view | create | update | delete         |
| ------- | ------------ | ------ | ------ | -------------- |
| Post    | Public       | Auth   | Owner  | Admin          |
| Comment | Public       | Auth   | Owner  | Owner or Admin |
| Tag     | Public       | Admin  | Admin  | Admin          |
| Vote    | —            | Auth   | —      | Owner          |

## User Roles

| Role    | Permissions                                   |
| ------- | --------------------------------------------- |
| Visitor | View posts, comments, vote counts             |
| User    | + Comment, vote                               |
| Admin   | + CRUD posts, manage tags, delete any comment |

### Remaining Polish

- [ ] Rate limiting on comment and vote routes
- [ ] Final visual polish pass

## Architectural Decisions

- **Inertia.js** — SPA experience without API layer
- **Wayfinder** — Type-safe route generation
- **Policies** — Authorization in policies, not FormRequests
- **PostResource** — Consistent API response transformation
- **Separation** — Public PostController vs Admin\PostController
- **Vote types in DB** — Scalable vote type system
- **One vote per user** — Unique constraint on votes table
- **Soft deletes** — Posts and comments use SoftDeletes
- **Sluggable** — spatie/laravel-sluggable for URL slugs

## License

All rights reserved.
