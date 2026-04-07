<?php

namespace Database\Seeders;

use App\Models\VoteType;
use Illuminate\Database\Seeder;

class VoteTypeSeeder extends Seeder
{
    public function run(): void
    {
        VoteType::firstOrCreate(['name' => 'up']);
        VoteType::firstOrCreate(['name' => 'down']);
    }
}
