<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Pages;

use App\Filament\Admin\Resources\MealCancellationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateMealCancellation extends CreateRecord
{
    protected static string $resource = MealCancellationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['is_handled']) {
            $data['handler_id'] = auth()->id();
            if (!isset($data['handled_until'])) {
                $data['handled_until'] = today();
            }
        }

        unset($data['is_handled']);

        $data['meals'] = collect($data['meals']);

        return $data;
    }

    protected function afterCreate()
    {
        Cache::forget('unhandled-by-meal');
    }
}
