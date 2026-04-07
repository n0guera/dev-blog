<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['content', 'post_id', 'user_id', 'parent_id'])]
class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
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
}
