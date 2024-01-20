<?php

namespace App\Filament\User\Resources\MealCancellationResource\Widgets;

use App\Models\Enums\MealType;
use App\Models\MealCancellation;
use App\Rules\AfterMealCancellationDeadlineRule;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class CreateMealCancellation extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.user.resources.meal-cancellation-resource.widgets.create-meal-cancellation';
    protected static bool $isLazy = false;
    public ?array $data = [];
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('create', MealCancellation::class);
    }

    public function mount(): void
    {

        $this->form->fill();
    }

    public function form(Form $form): Form
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
                    ->columnSpan('full'),
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
            ])
            ->columns([
                'default' => 1,
                'md' => 2,
            ])
            ->statePath('data');
    }

    public function create()
    {
        $this->authorize('create', MealCancellation::class);

        $this->data = $this->form->getState();
        $this->data['requester_id'] = auth()->id();

        MealCancellation::create($this->data);

        $this->data = [];
        $this->form->fill();

        $this->dispatch('meal-cancellation-created');
    }
}
