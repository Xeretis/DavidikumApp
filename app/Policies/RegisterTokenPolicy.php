<?php

namespace App\Policies;

use App\Models\RegisterToken;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegisterTokenPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(?User $user, RegisterToken $registerToken): bool
    {
        return $registerToken->used_at ? $user?->is_admin : true;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, RegisterToken $registerToken): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, RegisterToken $registerToken): bool
    {
        return $user->is_admin;
    }
}
