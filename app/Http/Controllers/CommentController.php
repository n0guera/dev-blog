<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function index(Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'votes', 'replies.user', 'replies.votes'])
            ->oldest()
            ->get();

        return response()->json(['comments' => $comments]);
    }

    public function store(CommentRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('create', Comment::class);

        $comment = new Comment($request->validated());
        $comment->post_id = $post->id;
        $comment->user_id = $request->user()->id;
        $comment->save();

        return redirect()->route('posts.show', $post->slug)
            ->with('success', 'Comment added successfully.');
    }

    public function update(CommentRequest $request, Comment $comment): RedirectResponse
    {
        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return redirect()->route('posts.show', $comment->post->slug)
            ->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $postSlug = $comment->post->slug;
        $comment->delete();

        return redirect()->route('posts.show', $postSlug)
            ->with('success', 'Comment deleted successfully.');
    }
}
