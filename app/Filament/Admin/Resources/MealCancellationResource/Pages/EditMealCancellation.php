<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Pages;

use App\Filament\Admin\Resources\MealCancellationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['is_handled'] = $data['handler_id'] !== null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['is_handled']) {
            $data['handler_id'] = auth()->id();
            if (!isset($data['handled_until'])) {
                $data['handled_until'] = today();
            }
        } else {
            $data['handler_id'] = null;
            $data['handled_until'] = null;
        }

        unset($data['is_handled']);

        $data['meals'] = collect($data['meals']);

        return $data;
    }

    protected function afterSave()
    {
        Cache::forget('unhandled-by-meal');
    }
}
