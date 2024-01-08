<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\MealCancellation;
use App\Models\RegisterToken;
use App\Models\User;
use App\Policies\MealCancellationPolicy;
use App\Policies\RegisterTokenPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        RegisterToken::class => RegisterTokenPolicy::class,
        User::class => UserPolicy::class,
        MealCancellation::class => MealCancellationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
