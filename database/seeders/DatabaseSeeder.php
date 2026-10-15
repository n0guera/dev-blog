<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed reference data in every environment and demo content locally only.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PostStatusSeeder::class,
            VoteTypeSeeder::class,
        ]);

        if (! app()->isLocal()) {
            return;
        }

        $this->seedDemoContent();
    }

    /**
     * Seed demo users and posts. Must only ever run in the local environment.
     */
    private function seedDemoContent(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole?->id,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role_id' => $userRole?->id,
        ]);

        $this->call([
            PostSeeder::class,
        ]);
    }
}
