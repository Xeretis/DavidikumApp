@php use App\Models\Enums\MealType;use App\Settings\MealSettings; @endphp
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Mai napon rendelendő étkezések
        </x-slot>

        <div class="flex flex-col gap-2">
            @if($amountToOrder->isEmpty())
                @foreach(app(MealSettings::class)->default_meal_amounts as $mealType => $value)
                    <div class="flex justify-between gap-4">
                        <x-filament::badge> {{ MealType::from($mealType)->getLabel() }}</x-filament::badge>
                        <p class="font-bold">
                            {{ $value }} db
                        </p>
                    </div>
                @endforeach
            @else
                @foreach($amountToOrder as $mealData)
                    <div class="flex justify-between gap-4">
                        <x-filament::badge> {{ MealType::from($mealData->meal_type)->getLabel() }}</x-filament::badge>
                        <p class="font-bold">
                            {{ $mealData->total }} db
                        </p>
                    </div>
                @endforeach
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
