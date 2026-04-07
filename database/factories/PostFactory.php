<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence();

        return [
            'user_id' => User::factory(),
            'status_id' => PostStatus::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'excerpt' => fake()->sentence(),
            'featured_image' => null,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status_id' => PostStatus::factory()->draft(),
            'published_at' => null,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status_id' => PostStatus::factory()->published(),
            'published_at' => now(),
        ]);
    }
}
