<?php

namespace App\Filament\Admin\Resources\RegisterTokenResource\Pages;

use App\Filament\Admin\Resources\RegisterTokenResource;
use App\Notifications\InviteNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateRegisterToken extends CreateRecord
{
    protected static string $resource = RegisterTokenResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return array_merge($data, [
            'token' => Str::random(32),
        ]);
    }

    protected function afterCreate(): void
    {
        $this->record->notify(new InviteNotification($this->record->token, $this->record->name));
    }
}
