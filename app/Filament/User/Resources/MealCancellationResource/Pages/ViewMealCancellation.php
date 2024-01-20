<?php

namespace App\Filament\User\Resources\MealCancellationResource\Pages;

use App\Filament\User\Resources\MealCancellationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMealCancellation extends ViewRecord
{
    protected static string $resource = MealCancellationResource::class;

    protected static ?string $navigationLabel = 'Megtekintése';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
