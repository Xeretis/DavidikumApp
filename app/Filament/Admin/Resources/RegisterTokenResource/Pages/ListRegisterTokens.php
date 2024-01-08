<?php

namespace App\Filament\Admin\Resources\RegisterTokenResource\Pages;

use App\Filament\Admin\Resources\RegisterTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegisterTokens extends ListRecords
{
    protected static string $resource = RegisterTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
