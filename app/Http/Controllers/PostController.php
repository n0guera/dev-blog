<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\PostStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the published posts.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::whereHas('status', fn ($q) => $q->where('name', 'published'))
            ->with(['user', 'status', 'tags'])
            ->withVotes()
            ->latest()
            ->paginate(12);

        return Inertia::render('posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): Response
    {
        $this->authorize('view', $post);

        $post->load(['user', 'tags', 'comments.user', 'comments.votes', 'status']);

        return Inertia::render('posts/Show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Post::class);

        return Inertia::render('admin/posts/Create', [
            'statuses' => PostStatus::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $post = new Post($request->validated());
        $post->user_id = $request->user()->id;
        $post->save();

        if ($request->has('tags')) {
            $post->tags()->attach($request->input('tags'));
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        return Inertia::render('admin/posts/Edit', [
            'post' => $post->load('tags'),
            'statuses' => PostStatus::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post->fill($request->validated());

        if ($request->has('featured_image') && $request->filled('featured_image')) {
            $post->featured_image = $request->input('featured_image');
        }

        $post->save();

        if ($request->has('tags')) {
            $post->tags()->sync($request->input('tags'));
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }

    /**
     * Display posts filtered by tag.
     */
    public function tagged(string $slug): Response
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::whereHas('status', fn ($q) => $q->where('name', 'published'))
            ->whereHas('tags', fn ($q) => $q->where('slug', $slug))
            ->with(['user', 'status', 'tags'])
            ->withVotes()
            ->latest()
            ->paginate(12);

        return Inertia::render('posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Search posts by query.
     */
    public function search(Request $request): Response
    {
        $this->authorize('viewAny', Post::class);

        $query = $request->query('q', '');

        $posts = Post::whereHas('status', fn ($q) => $q->where('name', 'published'))
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['user', 'status', 'tags'])
            ->withVotes()
            ->latest()
            ->paginate(12);

        return Inertia::render('posts/Search', [
            'posts' => $posts,
            'query' => $query,
        ]);
    }

    /**
     * Upload featured image for a post.
     */
    public function uploadImage(Request $request): array
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $image = $request->file('image');

        $width = (int) $image->getClientWidth();

        if ($width < 200 || $width > 1200) {
            return ['error' => 'Image width must be between 200 and 1200 pixels.'];
        }

        $filename = uniqid('post_').'.'.$image->getClientOriginalExtension();

        $image->storeAs('posts', $filename, 'public');

        return ['url' => "/storage/posts/{$filename}"];
    }
}
