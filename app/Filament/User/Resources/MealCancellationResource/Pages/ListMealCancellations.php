<?php

namespace App\Filament\User\Resources\MealCancellationResource\Pages;

use App\Filament\User\Resources\MealCancellationResource;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;

class ListMealCancellations extends ListRecords
{
    protected static string $resource = MealCancellationResource::class;

    protected ?string $subheading = 'Megjegyzés: Kezelt lemondásokat nem lehet törölni, csak módosítani.';

    #[On('meal-cancellation-created')]
    public function refresh()
    {

    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MealCancellationResource\Widgets\CreateMealCancellation::class
        ];
    }
}
