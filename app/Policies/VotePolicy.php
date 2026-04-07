<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vote;

final class VotePolicy
{
    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function delete(User $user, Vote $vote): bool
    {
        return $vote->user_id === $user->id;
    }
}
