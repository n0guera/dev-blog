<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\PostStatus;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    /**
     * Display a listing of the published posts.
     */
    public function index(): array
    {
        // TODO: Implementar paginación
        // TODO: Aplicar policy: Gate::allows('viewAny', Post::class) o $this->authorize('viewAny', Post::class)
        // TODO: Return Inertia render: return Inertia::render('posts/Index', [...]);
        return [];
    }

    /**
     * Display the specified post.
     */
    public function show(string $slug): array
    {
        // TODO: Buscar por slug, no por id
        // TODO: Policy: view
        // TODO: Cargar relaciones: user, tags, comments con sus votos, status
        // TODO: Return Inertia render: return Inertia::render('posts/Show', [...]);
        return [];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): array
    {
        // TODO: Policy: create
        // TODO: Return Inertia render: return Inertia::render('admin/posts/Create', [...]);
        return [
            'statuses' => PostStatus::all(),
        ];
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
    public function edit(Post $post): array
    {
        $this->authorize('update', $post);

        return [
            'post' => $post->load('tags'),
            'statuses' => PostStatus::all(),
        ];
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
    public function tagged(string $slug): array
    {
        // TODO: Buscar tag por slug, obtener posts publicados con ese tag
        // TODO: Policy: viewAny
        // TODO: Return Inertia render: return Inertia::render('posts/Index', [...]);
        return [];
    }

    /**
     * Search posts by query.
     */
    public function search(): array
    {
        // TODO: Obtener query param 'q', buscar en title y content
        // TODO: Policy: viewAny
        // TODO: Return Inertia render: return Inertia::render('posts/Search', [...]);
        return [];
    }

    /**
     * Upload featured image for a post.
     */
    public function uploadImage(PostRequest $request): array
    {
        // TODO: Policy
        // TODO: Validar archivo: imagen, max 2MB, dimensiones 200-1200px
        // TODO: Guardar en storage/app/public/posts/
        // TODO: Retornar la ruta de la imagen
        return [];
    }
}
