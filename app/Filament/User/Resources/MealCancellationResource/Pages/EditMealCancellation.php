<?php

namespace App\Filament\User\Resources\MealCancellationResource\Pages;

use App\Filament\User\Resources\MealCancellationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMealCancellation extends EditRecord
{
    protected static string $resource = MealCancellationResource::class;

    protected static ?string $navigationLabel = 'Szerkesztés';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['handled_until']);
        unset($data['handler_id']);

        return $data;
    }
}
