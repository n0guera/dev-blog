<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['user', 'admin']),
            'description' => fake()->sentence(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'name' => 'admin',
            'description' => 'Administrator with full access',
        ]);
    }

    public function user(): static
    {
        return $this->state(fn () => [
            'name' => 'user',
            'description' => 'Regular user with standard permissions',
        ]);
    }
}
