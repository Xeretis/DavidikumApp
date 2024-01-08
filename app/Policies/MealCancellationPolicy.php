<?php

namespace App\Policies;

use App\Models\MealCancellation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MealCancellationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, MealCancellation $mealCancellation): bool
    {
        return $user->is_admin || $user->id === $mealCancellation->requester_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MealCancellation $mealCancellation): bool
    {
        return $user->is_admin || $user->id === $mealCancellation->requester_id;
    }

    public function delete(User $user, MealCancellation $mealCancellation): bool
    {
        return $user->is_admin || $user->id === $mealCancellation->requester_id;
    }
}
