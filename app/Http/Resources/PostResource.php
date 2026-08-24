<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'featured_image' => $this->featured_image,
            'published_at' => $this->published_at?->toISOString(),
            'vote_score' => $this->vote_score,
            'up_votes_count' => $this->up_votes_count ?? 0,
            'down_votes_count' => $this->down_votes_count ?? 0,
            'user_vote' => $this->user_vote ?? null,
            'status' => $this->whenLoaded('status', fn () => [
                'id' => $this->status->id,
                'name' => $this->status->name,
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])
            ),
            'comments' => $this->whenLoaded('comments', fn () => $this->comments->map(fn ($comment) => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at->toISOString(),
                'user' => $comment->user ? [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ] : null,
                'vote_score' => $comment->vote_score ?? 0,
                'user_vote' => null,
                'replies' => $comment->replies ? $comment->replies->map(fn ($reply) => [
                    'id' => $reply->id,
                    'content' => $reply->content,
                    'created_at' => $reply->created_at->toISOString(),
                    'user' => $reply->user ? [
                        'id' => $reply->user->id,
                        'name' => $reply->user->name,
                    ] : null,
                    'vote_score' => $reply->vote_score ?? 0,
                    'user_vote' => null,
                    'replies' => [],
                ]) : [],
            ])),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
