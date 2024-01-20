<?php

use App\Filament\Admin\Resources\MealCancellationResource;
use App\Models\MealCancellation;
use App\Models\User;
use App\Settings\MealSettings;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('should allow access for admins', function () {
    $user = User::factory()->admin()->unverified()->create();

    actingAs($user);

    get(MealCancellationResource::getUrl(panel: 'admin'))
        ->assertOk();
});

it('should not allow access for regular users', function () {
    $user = User::factory()->unverified()->create();

    actingAs($user);

    get(MealCancellationResource::getUrl(panel: 'admin'))
        ->assertForbidden();
});

describe('as an authenticated user', function () {
    beforeEach(function () {
        filament()->setCurrentPanel(filament()->getPanel('admin'));
        $this->user = User::factory()->admin()->unverified()->create();

        actingAs($this->user);
    });

    it('can see table records', function () {
        $mealCancellations = MealCancellation::factory(10)->create();

        livewire(MealCancellationResource\Pages\ListMealCancellations::class)
            ->assertCanSeeTableRecords($mealCancellations);
    });

    it('can create if enabled', function () {
        $mealSettings = app(MealSettings::class);

        $mealSettings->is_meal_cancellation_enabled = true;

        get(MealCancellationResource::getUrl('create', panel: 'admin'))
            ->assertOk();
    });

    it('cannot create if disabled', function () {
        $mealSettings = app(MealSettings::class);

        $mealSettings->is_meal_cancellation_enabled = false;

        get(MealCancellationResource::getUrl('create', panel: 'admin'))
            ->assertForbidden();
    });

    it('can update', function () {
        $mealCancellation = MealCancellation::factory()->create();

        get(MealCancellationResource::getUrl('edit', [$mealCancellation->id], panel: 'admin'))
            ->assertOk();
    });

    it('can view', function () {
        $mealCancellation = MealCancellation::factory()->create();

        get(MealCancellationResource::getUrl('view', [$mealCancellation->id], panel: 'admin'))
            ->assertOk();
    });
});

