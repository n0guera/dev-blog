<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::with(['user', 'status', 'tags'])
            ->withVotes()
            ->latest()
            ->paginate(15);

        return Inertia::render('admin/posts/Index', ['posts' => PostResource::collection($posts)]);
    }

    public function create(): Response
    {
        $this->authorize('create', Post::class);

        return Inertia::render('admin/posts/Create', [
            'statuses' => PostStatus::all(),
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $post = new Post($request->validated());
        $post->user_id = $request->user()->id;
        $post->save();

        if ($request->has('tags')) {
            $post->tags()->attach($request->input('tags'));
        }

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        return Inertia::render('admin/posts/Edit', [
            'post' => new PostResource($post->load('tags')),
            'statuses' => PostStatus::all(),
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post->fill($request->validated());
        $post->save();

        if ($request->has('tags')) {
            $post->tags()->sync($request->input('tags'));
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    public function uploadImage(Request $request): array
    {
        $this->authorize('create', Post::class);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $image = $request->file('image');
        [$width] = getimagesize($image->getRealPath());

        if ($width < 200 || $width > 1200) {
            return ['error' => 'Image width must be between 200 and 1200 pixels.'];
        }

        $filename = uniqid('post_').'.'.$image->getClientOriginalExtension();
        $image->storeAs('posts', $filename, 'public');

        return ['url' => "/storage/posts/{$filename}"];
    }
}
