<?php

namespace App\Filament\Imports;

use App\Models\RegisterToken;
use App\Notifications\InviteNotification;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Get;
use Illuminate\Support\Str;

class RegisterTokenImporter extends Importer
{
    protected static ?string $model = RegisterToken::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Név')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Példa János'),
            ImportColumn::make('email')
                ->label('E-mail cím')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255', 'unique:users,email'])
                ->example('pelda.janos@example.com'),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('recreate_invitations')
                ->label('Meghívók újragenerálása')
                ->helperText('A meghívók újragenerálása a már meghívott felhasználóknak is.')
                ->live(),
            Checkbox::make('resend_invitations')
                ->label('Meghívók újraküldése')
                ->hidden(fn(Get $get) => !$get('recreate_invitations'))
                ->helperText('A meghívók újraküldése a már meghívott felhasználóknak is.')
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = number_format($import->successful_rows) . ' meghívandó felhasználó sikeresen importálva.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body = ' ' . number_format($failedRowsCount) . ' meghívandó felhasználó importálása sikertelen volt, hibás sorok miatt.';
        }

        return $body;
    }

    public function resolveRecord(): ?RegisterToken
    {
        if (!$this->options['recreate_invitations']) {
            return RegisterToken::firstOrNew([
                'email' => $this->data['email']
            ], [
                'token' => Str::random(32)
            ]);
        }

        return RegisterToken::newModelInstance([
            'token' => Str::random(32)
        ]);
    }

    protected function afterCreate(): void
    {
        /** @var RegisterToken $record */
        $record = $this->record;

        if (($record->wasRecentlyCreated && !$this->options['recreate_invitations']) || $this->options['resend_invitations'])
            $record->notify(new InviteNotification($record->token, $record->name));
    }
}
