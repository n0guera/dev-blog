<?php

namespace Database\Factories;

use App\Models\VoteType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VoteType>
 */
class VoteTypeFactory extends Factory
{
    protected $model = VoteType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Up', 'Down']),
        ];
    }

    public function up(): static
    {
        return $this->state(fn () => ['name' => 'up']);
    }

    public function down(): static
    {
        return $this->state(fn () => ['name' => 'down']);
    }
}
