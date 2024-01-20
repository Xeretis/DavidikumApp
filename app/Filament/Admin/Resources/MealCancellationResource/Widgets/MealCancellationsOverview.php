<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Widgets;

use App\Models\MealCancellation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;

class MealCancellationsOverview extends BaseWidget
{

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $mealCancellationsCount = MealCancellation::count();

        $mealCancellationsCountTrend = Trend::model(MealCancellation::class)
            ->between(
                start: now()->subMonths(12),
                end: now(),
            )
            ->perMonth()
            ->count();

        $totalCancelledMeals = MealCancellation::query()
            ->select(DB::raw('sum((date(end_date) - date(start_date) + 1) * json_array_length(meals::json)) as total'))
            ->value('total');

        $totalUnhandledCancelledMeals = MealCancellation::query()
            ->select(DB::raw("sum((CASE WHEN handled_until IS NULL THEN date(end_date) - date(start_date) + 1 ELSE date(end_date) - date(handled_until) END) * json_array_length(meals::json)) as total"))
            ->value('total');

        return [
            Stat::make('Összes lemondás', $mealCancellationsCount)
                ->icon('heroicon-o-list-bullet')
                ->color('primary')
                ->chart($mealCancellationsCountTrend->map(fn(TrendValue $value) => $value->aggregate)->toArray()),
            Stat::make('Összes lemondott étkezés', $totalCancelledMeals)
                ->icon('heroicon-o-information-circle'),
            Stat::make('Nem kezelt lemondott étkezések', $totalUnhandledCancelledMeals)
                ->icon('heroicon-o-exclamation-circle'),
        ];
    }
}
