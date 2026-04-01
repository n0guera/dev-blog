<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\PostStatus;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): array
    {
        return [
            'statuses' => PostStatus::all(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): RedirectResponse
    {
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
    public function edit(string $id): array
    {
        $post = Post::findOrFail($id);

        return [
            'post' => $post->load('tags'),
            'statuses' => PostStatus::all(),
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        $post->fill($request->validated());
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
    public function destroy(string $id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
