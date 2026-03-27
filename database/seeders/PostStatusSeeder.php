<?php

namespace Database\Seeders;

use App\Models\PostStatus;
use Illuminate\Database\Seeder;

class PostStatusSeeder extends Seeder
{
    public function run(): void
    {
        PostStatus::firstOrCreate(['name' => 'draft']);
        PostStatus::firstOrCreate(['name' => 'published']);
    }
}
