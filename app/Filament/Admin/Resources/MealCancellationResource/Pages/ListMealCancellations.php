<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Pages;

use App\Filament\Admin\Resources\MealCancellationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMealCancellations extends ListRecords
{
    protected static string $resource = MealCancellationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
