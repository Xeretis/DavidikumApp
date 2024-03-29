<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MealCancellationResource\Pages;
use App\Filament\Admin\Resources\MealCancellationResource\RelationManagers;
use App\Filament\Admin\Resources\MealCancellationResource\Widgets\AmountToOrder;
use App\Filament\Admin\Resources\MealCancellationResource\Widgets\MealCancellationsOverview;
use App\Filament\Admin\Resources\MealCancellationResource\Widgets\UnhandledMealCancellations;
use App\Filament\Admin\Resources\UserResource\Pages\ViewUser;
use App\Models\Enums\MealType;
use App\Models\MealCancellation;
use App\Rules\AfterMealCancellationDeadlineRule;
use Carbon\CarbonPeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class MealCancellationResource extends Resource
{
    protected static ?string $model = MealCancellation::class;

    protected static ?string $navigationIcon = 'heroicon-o-backspace';

    protected static ?string $modelLabel = 'étkezés lemondás';
    protected static ?string $pluralModelLabel = 'Étkezés lemondások';
    protected static ?string $navigationGroup = 'Étkeztetés';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addMonth();

        $period = new CarbonPeriod($startDate, $endDate);

        $weekendDays = [];
        foreach ($period as $date) {
            if ($date->isWeekend()) {
                $weekendDays[] = $date->format('Y-m-d');
            }
        }


        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('meals')
                        ->label('Érintett étkezések')
                        ->multiple()
                        ->options(MealType::class)
                        ->selectablePlaceholder(false)
                        ->required()
                        ->columnSpan(2),
                    Forms\Components\Datepicker::make('start_date')
                        ->label('Lemondás kezdete')
                        ->date()
                        ->native(false)
                        ->required()
                        ->disabledDates($weekendDays)
                        ->minDate(today())
                        ->maxDate(now()->addMonth())
                        ->rules([fn(Forms\Get $get) => new AfterMealCancellationDeadlineRule($get('end_date'))]),
                    Forms\Components\Datepicker::make('end_date')
                        ->label('Lemondás vége')
                        ->date()
                        ->native(false)
                        ->required()
                        ->minDate(today())
                        ->afterOrEqual('start_date'),
                    Forms\Components\DatePicker::make('handled_until')
                        ->label('Kezelve eddig')
                        ->helperText('Ha megjelölöd ezt a lemondást kezeltként és ezt a mezőt üresen hagyod, akkor ez automatikusan a mai nap lesz.')
                        ->date()
                        ->native(false)
                        ->disabled(fn(Forms\Get $get) => $get('is_handled') === false)
                        ->afterOrEqual('start_date')
                        ->beforeOrEqual('end_date')
                        ->columnSpan(2),
                ])->columns(),
                Forms\Components\Section::make([
                    Forms\Components\Select::make('requester_id')
                        ->label('Lemondás kezdeményezője')
                        ->relationship('requester', 'name', modifyQueryUsing: fn($query) => $query->where('is_admin', false))
                        ->preload()
                        ->searchable()
                        ->required(),
                    Forms\Components\Toggle::make('is_handled')
                        ->label('Lemondás kezelve')
                        ->hint('(általad)')
                        ->inline(false)
                        ->required()
                        ->live(),

                ])->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('meals')
                    ->label('Érintett étkezések')
                    ->sortable()
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Lemondás kezdete')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Lemondás vége')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Lemondás kezdeményezője')
                    ->color('danger')
                    ->icon('heroicon-o-user-circle')
                    ->badge()
                    ->url(fn(MealCancellation $record) => ViewUser::getUrl([$record->requester_id]))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('handled_until')
                    ->label('Kezelve eddig')
                    ->placeholder('Nincs kezelve')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('handler.name')
                    ->label('Lemondás kezelője')
                    ->color('success')
                    ->icon('heroicon-o-user-circle')
                    ->badge()
                    ->placeholder('Nincs kezelve')
                    ->url(fn(?MealCancellation $record) => $record->handler_id !== null ? ViewUser::getUrl([$record->handler_id]) : null)
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Módosítva')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('start_date')
                            ->label('Lemondás kezdete'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('end_date')
                            ->label('Lemondás vége'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('handled_until')
                            ->label('Kezelve eddig'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('requester')
                            ->label('Lemondás kezdeményezője')
                            ->selectable(IsRelatedToOperator::make()
                                ->titleAttribute('name')
                                ->multiple()
                                ->preload()
                                ->searchable()),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('handler')
                            ->label('Lemondás kezelője')
                            ->selectable(IsRelatedToOperator::make()
                                ->titleAttribute('name')
                                ->multiple()
                                ->preload()
                                ->searchable()),
                    ]),
                Tables\Filters\SelectFilter::make('meals')
                    ->label('Érintett étkezések')
                    ->options(MealType::class)
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        if (count($data['values']) === 0) {
                            return $query;
                        }

                        $query->where(function ($query) use ($data) {
                            $firstMeal = array_shift($data['values']);
                            $query->whereJsonContains('meals', $firstMeal);

                            foreach ($data['values'] as $meal) {
                                $query->orWhereJsonContains('meals', $meal);
                            }
                        });

                        return $query;
                    }),
                Tables\Filters\TernaryFilter::make('is_handled')
                    ->label('Kezelve')
                    ->nullable()
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('handler'),
                        false: fn(Builder $query) => $query->whereDoesntHave('handler'),
                        blank: fn(Builder $query) => $query
                    ),
                Tables\Filters\TernaryFilter::make('is_fully_handled')
                    ->label('Teljesen kezelve')
                    ->nullable()
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('handler', fn(Builder $query) => $query->whereColumn('handled_until', '=', 'end_date')),
                        false: fn(Builder $query) => $query->whereHas('handler', fn(Builder $query) => $query->whereColumn('handled_until', '<', 'end_date'))->orWhereDoesntHave('handler'),
                        blank: fn(Builder $query) => $query
                    ),
            ])
            ->filtersFormWidth(MaxWidth::Large)
            ->actions([
                Tables\Actions\ViewAction::make()->label('Részletek')->icon('heroicon-m-information-circle')->color('primary'),
                Tables\Actions\DeleteAction::make()->after(fn() => Cache::forget('unhandled-by-meal')),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->after(fn() => Cache::forget('unhandled-by-meal')),
                    Tables\Actions\BulkAction::make('handle')
                        ->label('Megjelölés kezeltként')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['handler_id' => auth()->id(), 'handled_until' => today()]);

                            Cache::forget('unhandled-by-meal');
                        })
                ]),
            ])->modifyQueryUsing(fn($query) => $query->with(['requester:id,name', 'handler:id,name']))
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Grid::make([
                        'default' => 1
                    ])->schema([
                        Section::make([
                            TextEntry::make('meals')
                                ->label('Érintett étkezések')
                                ->badge(),
                            TextEntry::make('start_date')
                                ->label('Lemondás kezdete')
                                ->date(),
                            TextEntry::make('end_date')
                                ->label('Lemondás vége')
                                ->date(),
                            TextEntry::make('handled_until')
                                ->label('Kezelve eddig')
                                ->placeholder('Nincs kezelve')
                                ->date(),
                        ])->columns(),
                        Section::make([
                            TextEntry::make('requester.name')
                                ->label('Lemondás kezdeményezője')
                                ->color('danger')
                                ->badge()
                                ->icon('heroicon-o-user-circle')
                                ->url(fn(MealCancellation $record) => ViewUser::getUrl([$record->requester_id])),
                            TextEntry::make('handler.name')
                                ->label('Lemondás kezelője')
                                ->color('success')
                                ->badge()
                                ->icon('heroicon-o-user-circle')
                                ->url(fn(?MealCancellation $record) => $record->handler_id !== null ? ViewUser::getUrl([$record->handler_id]) : null)
                                ->placeholder('Nincs kezelve'),
                        ])->columns(),
                    ])->grow(),
                    Section::make([
                        TextEntry::make('created_at')
                            ->label('Létrehozva')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Módosítva')
                            ->dateTime(),
                    ])->grow(false),
                ])->from('lg'),
            ])->columns(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            AmountToOrder::class,
            MealCancellationsOverview::class,
            UnhandledMealCancellations::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMealCancellations::route('/'),
            'create' => Pages\CreateMealCancellation::route('/create'),
            'view' => Pages\ViewMealCancellation::route('/{record}'),
            'edit' => Pages\EditMealCancellation::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewMealCancellation::class,
            Pages\EditMealCancellation::class,
        ]);
    }
}
