<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MealCancellationResource\Pages;
use App\Filament\User\Resources\MealCancellationResource\RelationManagers;
use App\Filament\User\Resources\MealCancellationResource\Widgets\CreateMealCancellation;
use App\Models\Enums\MealType;
use App\Models\MealCancellation;
use App\Rules\AfterMealCancellationDeadlineRule;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
use Illuminate\Support\Carbon;

class MealCancellationResource extends Resource
{
    protected static ?string $model = MealCancellation::class;

    protected static ?string $navigationIcon = 'heroicon-o-backspace';

    protected static ?string $modelLabel = 'étkezés lemondás';
    protected static ?string $pluralModelLabel = 'Étkezés lemondások';
    
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
                Select::make('meals')
                    ->label('Érintett étkezések')
                    ->multiple()
                    ->options(MealType::class)
                    ->selectablePlaceholder(false)
                    ->required()
                    ->disabled(fn(Get $get) => $get('handled_until') !== null)
                    ->columnSpan(2),
                Datepicker::make('start_date')
                    ->label('Lemondás kezdete')
                    ->date()
                    ->native(false)
                    ->required()
                    ->disabled(fn(Get $get) => $get('handled_until') !== null)
                    ->disabledDates($weekendDays)
                    ->minDate(today())
                    ->maxDate(now()->addMonth())
                    ->rules([fn(Get $get) => new AfterMealCancellationDeadlineRule($get('end_date'))]),
                Datepicker::make('end_date')
                    ->label('Lemondás vége')
                    ->date()
                    ->native(false)
                    ->required()
                    ->minDate(today())
                    ->afterOrEqual('start_date')
                    ->afterOrEqual('handled_until'),
                DatePicker::make('handled_until')
                    ->label('Kezelve eddig')
                    ->disabled()
                    ->validationAttribute('a \'Kezelve eddig\' értéke')
                    ->columnSpan(2)
                    ->hidden(fn(Get $get) => $get('handled_until') === null),
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
                    ->searchable()
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Lemondás vége')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('date_range')
                    ->state(fn($record) => $record->start_date->format('Y. m. d.') . ' - ' . $record->end_date->format('Y. m. d.'))
                    ->label('Lemondás időtartama')
                    ->sortable()
                    ->searchable()
                    ->hiddenFrom('md'),
                Tables\Columns\TextColumn::make('handler.name')
                    ->label('Lemondás kezelője')
                    ->color('success')
                    ->icon('heroicon-o-user-circle')
                    ->badge()
                    ->placeholder('Nincs kezelve')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('handled_until')
                    ->label('Kezelve eddig')
                    ->placeholder('Nincs kezelve')
                    ->date()
                    ->sortable(),
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
                Tables\Actions\ViewAction::make()->label('Kezelés')->icon('heroicon-m-wrench-screwdriver')->color('primary'),
                Tables\Actions\DeleteAction::make()->disabled(fn($record) => $record->handled_until !== null)
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('requester_id', auth()->id())->with('handler:id,name');
            })
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
                                ->badge()
                                ->columnSpan(2),
                            TextEntry::make('start_date')
                                ->label('Lemondás kezdete')
                                ->date(),
                            TextEntry::make('end_date')
                                ->label('Lemondás vége')
                                ->date(),
                        ])->columns(),
                        Section::make([
                            TextEntry::make('handler.name')
                                ->label('Lemondás kezelője')
                                ->color('success')
                                ->badge()
                                ->icon('heroicon-o-user-circle')
                                ->placeholder('Nincs kezelve'),
                            TextEntry::make('handled_until')
                                ->label('Kezelve eddig')
                                ->placeholder('Nincs kezelve')
                                ->date(),
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewMealCancellation::class,
            Pages\EditMealCancellation::class,
        ]);
    }

    public static function getWidgets(): array
    {
        return [
            CreateMealCancellation::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMealCancellations::route('/'),
            'view' => Pages\ViewMealCancellation::route('/{record}'),
            'edit' => Pages\EditMealCancellation::route('/{record}/edit'),
        ];
    }
}
