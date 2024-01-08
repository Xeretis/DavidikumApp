<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Pages;

use App\Filament\Admin\Resources\MealCancellationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMealCancellation extends ViewRecord
{
    protected static string $resource = MealCancellationResource::class;

    protected static ?string $navigationLabel = 'Megtekintése';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
