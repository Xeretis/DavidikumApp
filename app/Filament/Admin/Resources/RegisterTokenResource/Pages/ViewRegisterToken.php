<?php

namespace App\Filament\Admin\Resources\RegisterTokenResource\Pages;

use App\Filament\Admin\Resources\RegisterTokenResource;
use App\Models\RegisterToken;
use App\Notifications\InviteNotification;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewRegisterToken extends ViewRecord
{
    protected static string $resource = RegisterTokenResource::class;

    protected static ?string $navigationLabel = 'Megtekintés';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Újraküldés')
                ->requiresConfirmation()
                ->color('gray')
                ->icon('heroicon-o-paper-airplane')
                ->action(fn(RegisterToken $record) => $record->notify(new InviteNotification($record->token, $record->name))),
            Actions\DeleteAction::make(),
        ];
    }
}
