<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[Fillable(['title', 'slug', 'content', 'excerpt', 'featured_image', 'status_id', 'published_at'])]
class Post extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    public const LATEST_POSTS_CACHE_KEY = 'latest_posts';

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::LATEST_POSTS_CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::LATEST_POSTS_CACHE_KEY));
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(PostStatus::class, 'status_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function upVotes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable')
            ->whereHas('voteType', fn ($q) => $q->where('name', 'up'));
    }

    public function downVotes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable')
            ->whereHas('voteType', fn ($q) => $q->where('name', 'down'));
    }

    public function scopeWithVotes($query): Builder
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
