<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Pages;

use App\Filament\Admin\Resources\MealCancellationResource;
use App\Filament\Exports\MealCancellationExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
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
            Actions\ExportAction::make()->label('Exportálás')->exporter(MealCancellationExporter::class)->modifyQueryUsing(function (Builder $query) {
                return $query->with(['requester:id,name', 'handler:id,name'])->reorder();
            }),
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
