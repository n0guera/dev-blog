<?php

namespace Database\Factories;

use App\Models\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostStatus>
 */
class PostStatusFactory extends Factory
{
    protected $model = PostStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Draft', 'Published']),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['name' => 'draft']);
    }

    public function published(): static
    {
        return $this->state(fn () => ['name' => 'published']);
    }
}
