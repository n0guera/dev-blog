<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

final class PostPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Post $post): bool
    {
        return $user->isAdmin() && $post->user_id === $user->id;
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}
