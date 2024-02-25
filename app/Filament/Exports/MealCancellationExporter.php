<?php

namespace App\Filament\Exports;

use App\Models\MealCancellation;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MealCancellationExporter extends Exporter
{
    protected static ?string $model = MealCancellation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('meals')
                ->label('Érintett étkezések'),
            ExportColumn::make('start_date')
                ->label('Lemondás kezdete'),
            ExportColumn::make('end_date')
                ->label('Lemondás vége'),
            ExportColumn::make('requester.name')
                ->label('Lemondás kezdeményezője'),
            ExportColumn::make('handled_until')
                ->label('Kezelve eddig'),
            ExportColumn::make('handler.name')
                ->label('Lemondás kezelője'),
            ExportColumn::make('created_at')
                ->label('Létrehozva'),
            ExportColumn::make('updated_at')
                ->label('Módosítva'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Befejeződött az étkezés lemondások exportálása. ' . number_format($export->successful_rows) . ' sor került exportálásra.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' sor exportálása sikertelen volt.';
        }

        return $body;
    }
}
