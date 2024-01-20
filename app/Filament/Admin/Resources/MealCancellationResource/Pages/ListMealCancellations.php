<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Pages;

use App\Filament\Admin\Resources\MealCancellationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;

class  ListMealCancellations extends ListRecords
{
    protected static string $resource = MealCancellationResource::class;

    #[On('meal-cancellations-handled')]
    public function refresh()
    {
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MealCancellationResource\Widgets\MealCancellationsOverview::class,
            MealCancellationResource\Widgets\UnhandledMealCancellations::class,
            MealCancellationResource\Widgets\AmountToOrder::class
        ];
    }
}
