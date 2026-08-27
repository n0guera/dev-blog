<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Vote;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::whereHas('status', fn ($q) => $q->where('name', 'published'))
            ->with(['user', 'status', 'tags'])
            ->withVotes()
            ->latest()
            ->paginate(12);

        return Inertia::render('posts/Index', ['posts' => PostResource::collection($posts)]);
    }

    public function show(Post $post): Response
    {
        $post->load(['user', 'tags', 'status']);
        $post->loadCount(['upVotes', 'downVotes']);
        $post->load(['comments' => function ($query) {
            $query->whereNull('parent_id')
                ->with(['user', 'votes', 'replies.user', 'replies.votes'])
                ->oldest();
        }]);

        if (auth()->check()) {
            $userVote = Vote::where('votable_type', Post::class)
                ->where('votable_id', $post->id)
                ->where('user_id', auth()->id())
                ->first();

            $post->user_vote = $userVote ? $userVote->voteType->name : null;
        } else {
            $post->user_vote = null;
        }

        return Inertia::render('posts/Show', ['post' => new PostResource($post)]);
    }

    public function tagged(Tag $tag): Response
    {
        $posts = Post::whereHas('status', fn ($q) => $q->where('name', 'published'))
            ->whereHas('tags', fn ($q) => $q->where('id', $tag->id))
            ->with(['user', 'status', 'tags'])
            ->withVotes()
            ->latest()
            ->paginate(12);

        return Inertia::render('posts/Index', ['posts' => PostResource::collection($posts), 'tag' => $tag]);
    }

    public function search(Request $request): Response
    {
        $query = $request->query('q', '');

        $posts = Post::whereHas('status', fn ($q) => $q->where('name', 'published'))
            ->where(fn ($q) => $q
                ->where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
            )
            ->with(['user', 'status', 'tags'])
            ->withVotes()
            ->latest()
            ->paginate(12);

        return Inertia::render('posts/Search', ['posts' => PostResource::collection($posts), 'query' => $query]);
    }
}
