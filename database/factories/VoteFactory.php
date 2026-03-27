<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoteType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vote>
 */
class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'votable_type' => Post::class,
            'votable_id' => Post::factory(),
            'vote_type_id' => VoteType::factory(),
        ];
    }

    public function upvote(): static
    {
        return $this->state(fn () => [
            'vote_type_id' => VoteType::factory()->up(),
        ]);
    }

    public function downvote(): static
    {
        return $this->state(fn () => [
            'vote_type_id' => VoteType::factory()->down(),
        ]);
    }
}
