<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Providers\RouteServiceProvider;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $navigationLabel = 'Megtekintése';

    protected function getHeaderActions(): array
    {
        return [
            Impersonate::make()->label('Megszemélyesítés')->redirectTo(RouteServiceProvider::HOME),
            DeleteAction::make(),
        ];
    }
}
