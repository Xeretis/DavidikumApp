<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Imports\RegisterTokenImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->label('Felhasználók meghívása')
                ->modelLabel('Meghívandó felhasználó')
                ->pluralModelLabel('Meghívandó felhasználók')
                ->importer(RegisterTokenImporter::class),
        ];
    }
}
