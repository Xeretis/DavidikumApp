<?php

namespace App\Filament\Admin\Resources\MealCancellationResource\Widgets;

use App\Models\Enums\MealType;
use App\Settings\MealSettings;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class AmountToOrder extends Widget
{
    protected static string $view = 'filament.admin.resources.meal-cancellation-resource.widgets.amount-to-order';

    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        $today = today();

        $unhandledAmount = DB::query()->from('meal_cancellations')
            ->selectRaw("json_array_elements_text(meals::json) as meal_type, count(*) as total")
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->groupBy('meal_type')
            ->get();

        $amountToOrder = $unhandledAmount->map(function ($item) {
            return (object)[
                'meal_type' => $item->meal_type,
                'total' => app(MealSettings::class)->default_meal_amounts[$item->meal_type] - $item->total
            ];
        });

        return [
            'amountToOrder' => $amountToOrder->sortBy(fn($item) => MealType::from($item->meal_type)->getSortOrder())
        ];
    }
}
