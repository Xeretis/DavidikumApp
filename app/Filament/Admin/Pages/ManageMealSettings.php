<?php

namespace App\Filament\Admin\Pages;

use App\Data\MealCancellationDeadlineData;
use App\Settings\MealSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageMealSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = MealSettings::class;

    protected static ?string $title = 'Étkeztetési beállítások';
    protected static ?string $navigationGroup = 'Étkeztetés';
    protected static ?int $navigationSort = 20;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('is_meal_cancellation_enabled')
                    ->label('Étkezés lemondás engedélyezése')
                    ->helperText('Ha engedélyezve van, akkor a felhasználók lemondhatják az étkezéseiket.')
                    ->inline(false)
                    ->required(),
                Section::make('Alapértelmezett rendelési mennyiségek')->schema([
                    TextInput::make('default_meal_amounts.breakfast')
                        ->label('Reggeli')
                        ->type('number')
                        ->numeric()
                        ->minValue(0),
                    TextInput::make('default_meal_amounts.morning_snack')
                        ->label('Tízórai')
                        ->type('number')
                        ->minValue(0),
                    TextInput::make('default_meal_amounts.lunch')
                        ->label('Ebéd')
                        ->type('number')
                        ->numeric()
                        ->minValue(0),
                    TextInput::make('default_meal_amounts.afternoon_snack')
                        ->label('Uzsonna')
                        ->type('number')
                        ->numeric()
                        ->minValue(0),
                    TextInput::make('default_meal_amounts.dinner')
                        ->label('Vacsora')
                        ->type('number')
                        ->numeric()
                        ->minValue(0)

                ]),
                Repeater::make('meal_cancellation_deadlines')
                    ->label('Étkezés lemondás határidők')
                    ->addActionLabel('Határidő hozzáadása')
                    ->schema([
                        Select::make('daysOfWeek')
                            ->label('Nap')
                            ->multiple()
                            ->options([
                                'monday' => 'Hétfő',
                                'tuesday' => 'Kedd',
                                'wednesday' => 'Szerda',
                                'thursday' => 'Csütörtök',
                                'friday' => 'Péntek',
                                'saturday' => 'Szombat',
                                'sunday' => 'Vasárnap',
                            ])
                            ->required()
                            ->distinct()
                            ->live(),
                        TextInput::make('hoursBefore')
                            ->label('A napot/napokat megelőző órák száma')
                            ->helperText(function ($state) {
                                if ($state <= 24)
                                    return 'Így az étkezés lemondható előző nap ' . 24 - $state . ' óráig.';
                                else
                                    return 'Így az étkezés lemondható ' . floor($state / 24) + 1 . ' nappal előbb ' . 24 - ($state % 24) . ' óráig.';
                            })
                            ->live()
                            ->numeric()
                            ->default(16)
                            ->minValue(0)
                    ])
            ])->columns(false);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['meal_cancellation_deadlines'] = MealCancellationDeadlineData::collection($data['meal_cancellation_deadlines']);

        return $data;
    }
}
