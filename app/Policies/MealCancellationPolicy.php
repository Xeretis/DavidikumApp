<?php

namespace App\Policies;

use App\Models\MealCancellation;
use App\Models\User;
use App\Settings\MealSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class MealCancellationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MealCancellation $mealCancellation): bool
    {
        return $user->is_admin || $user->id === $mealCancellation->requester_id;
    }

    public function create(User $user): bool
    {
        return app(MealSettings::class)->is_meal_cancellation_enabled;
    }

    public function update(User $user, MealCancellation $mealCancellation): bool
    {
        return $user->is_admin || ($user->id === $mealCancellation->requester_id && $mealCancellation->handled_until !== $mealCancellation->end_date && app(MealSettings::class)->is_meal_cancellation_enabled);
    }

    public function delete(User $user, MealCancellation $mealCancellation): bool
    {
        return $user->is_admin || $user->id === $mealCancellation->requester_id;
    }
}
