<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RegisterTokenResource\Pages;
use App\Filament\Admin\Resources\RegisterTokenResource\RelationManagers;
use App\Models\RegisterToken;
use Filament\Forms\Components\TextInput;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RegisterTokenResource extends Resource
{
    protected static ?string $model = RegisterToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $modelLabel = 'meghívó';

    protected static ?string $pluralLabel = 'Meghívók';

    protected static ?string $navigationGroup = 'Felhasználók';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Név')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mail cím')
                    ->email()
                    ->unique('users', 'email')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Név')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail cím')
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('used_at')
                    ->label('Elfogadva')
                    ->placeholder('Nincs elfogadva')
                    ->dateTime()
                    ->since()
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sent')
                    ->label('Utoljára elküldve')
                    ->placeholder('Nincs elküldve')
                    ->state( // We specifically want to work with the collection and not the query to avoid N+1 issues
                        fn(RegisterToken $record) => $record->notifications->sortByDesc('created_at')->first()?->created_at
                    )
                    ->dateTime()
                    ->since()
                    ->badge()
                    ->color('info'),
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
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')->label('Név'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('email')->label('E-mail cím'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('used_at')->label('Elfogadva'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')->label('Létrehozva'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')->label('Módosítva'),
                    ]),
                Tables\Filters\TernaryFilter::make('used')
                    ->label('Elküldve')
                    ->nullable()
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('notifications'),
                        false: fn(Builder $query) => $query->whereDoesntHave('notifications'),
                        blank: fn(Builder $query) => $query
                    ),
            ])
            ->filtersFormWidth(MaxWidth::Large)
            ->actions([
                Tables\Actions\ViewAction::make()->label('Kezelés')->icon('heroicon-m-wrench-screwdriver')->color('primary'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
                            TextEntry::make('name')
                                ->label('Név'),
                            TextEntry::make('email')
                                ->label('E-mail cím')
                                ->copyable(),
                            TextEntry::make('used_at')
                                ->label('Elfogadva')
                                ->placeholder('Nincs elfogadva')
                                ->dateTime()
                                ->since()
                                ->badge()
                                ->color('success'),
                        ])->columns(),
                        Section::make([
                            TextEntry::make('sent')
                                ->label('Utoljára elküldve')
                                ->placeholder('Nincs elküldve')
                                ->state(fn(RegisterToken $record) => $record->notifications->sortByDesc('created_at')->first()?->created_at)
                                ->dateTime()
                                ->since()
                                ->badge()
                                ->color('info'),
                            TextEntry::make('sent_count')
                                ->label('Elküldések száma')
                                ->state(fn(RegisterToken $record) => $record->notifications()->count())
                                ->badge()
                                ->color('info'),
                        ])->columns(),
                        Section::make([
                            TextEntry::make('invite_url')
                                ->label('Link')
                                ->helperText('Ez a link csak akkor működik, ha a meghívó még nincs elfogadva és nincs bejelentkezve felhasználó.')
                                ->state(fn(RegisterToken $record) => route('filament.common.auth.register', ['t' => $record->token]))
                                ->url(fn(RegisterToken $record) => route('filament.common.auth.register', ['t' => $record->token]))
                                ->color('primary')
                                ->copyable(),
                        ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegisterTokens::route('/'),
            'create' => Pages\CreateRegisterToken::route('/create'),
            'view' => Pages\ViewRegisterToken::route('/{record}'),
            'edit' => Pages\EditRegisterToken::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewRegisterToken::class,
            Pages\EditRegisterToken::class,
        ]);
    }
}
