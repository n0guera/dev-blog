<?php

namespace App\Http\Middleware;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'tags' => Cache::remember(
                'nav_tags',
                3600,
                fn () => Tag::withCount('posts')
                    ->orderByDesc('posts_count')
                    ->limit(15)
                    ->get(['name', 'slug'])
                    ->map(fn ($tag) => [
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                        'url' => '/tags/'.$tag->slug,
                    ])
            ),
        ];
    }
}
