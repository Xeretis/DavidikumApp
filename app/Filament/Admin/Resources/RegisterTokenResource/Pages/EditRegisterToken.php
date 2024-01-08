<?php

namespace App\Filament\Admin\Resources\RegisterTokenResource\Pages;

use App\Filament\Admin\Resources\RegisterTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegisterToken extends EditRecord
{
    protected static string $resource = RegisterTokenResource::class;

    protected static ?string $navigationLabel = 'Szerkesztés';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
