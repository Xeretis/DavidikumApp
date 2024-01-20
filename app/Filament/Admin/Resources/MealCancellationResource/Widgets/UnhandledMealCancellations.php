<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Widgets;

use App\Models\Enums\MealType;
use App\Models\MealCancellation;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UnhandledMealCancellations extends Widget
{
    protected static string $view = 'filament.admin.resources.meal-cancellation-resource.widgets.unhandled-meal-cancellations';

    protected static bool $isLazy = false;

    public function handleAll()
    {
        $today = Carbon::today();

        MealCancellation::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where(function ($query) use ($today) {
                $query->where('handled_until', '<', $today)
                    ->orWhereNull('handled_until');
            })
            ->update([
                'handler_id' => auth()->id(),
                'handled_until' => today(),
            ]);

        Cache::forget('unhandled-by-meal');
        $this->dispatch('meal-cancellations-handled');
    }

    protected function getViewData(): array
    {
        $unhandledByMeal = Cache::remember('unhandled-by-meal', 60, function () {
            $today = today();

            return DB::query()->from('meal_cancellations')
                ->selectRaw("json_array_elements_text(meals::json) as meal_type, count(*) as total")
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->where(function ($query) use ($today) {
                    $query->where('handled_until', '<', $today)
                        ->orWhereNull('handled_until');
                })
                ->groupBy('meal_type')
                ->get();
        });

        return [
            'unhandledByMeal' => $unhandledByMeal->sortBy(fn($item) => MealType::from($item->meal_type)->getSortOrder()),
        ];
    }
}
