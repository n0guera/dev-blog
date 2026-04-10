<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function index(): Response
    {
        $tags = Tag::withCount('posts')
            ->orderByDesc('posts_count')
            ->get();

        return Inertia::render('tags/Index', ['tags' => $tags]);
    }
}
