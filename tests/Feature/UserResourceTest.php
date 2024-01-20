<?php

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('should allow access for admins', function () {
    $user = User::factory()->admin()->unverified()->create();

    actingAs($user);

    get(UserResource::getUrl(panel: 'admin'))
        ->assertOk();
});

it('should not allow access for regular users', function () {
    $user = User::factory()->unverified()->create();

    actingAs($user);

    get(UserResource::getUrl(panel: 'admin'))
        ->assertForbidden();
});

describe('as an authenticated user', function () {
    beforeEach(function () {
        filament()->setCurrentPanel(filament()->getPanel('admin'));
        $this->user = User::factory()->admin()->unverified()->create();

        actingAs($this->user);
    });

    it('can see table records', function () {
        $users = User::factory(9)->unverified()->create(); // we already have a user so we only create 9 here

        livewire(UserResource\Pages\ListUsers::class)
            ->assertCanSeeTableRecords($users);
    });

    it('can create', function () {
        get(UserResource::getUrl('create', panel: 'admin'))
            ->assertOk();
    });

    it('can update', function () {
        $user = User::factory()->create();

        get(UserResource::getUrl('edit', [$user->id], panel: 'admin'))
            ->assertOk();
    });

    it('can view', function () {
        $user = User::factory()->create();

        get(UserResource::getUrl('view', [$user->id], panel: 'admin'))
            ->assertOk();
    });
});
