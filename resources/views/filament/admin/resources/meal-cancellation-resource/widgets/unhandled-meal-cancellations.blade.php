@php use App\Models\Enums\MealType;use App\Settings\MealSettings; @endphp
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Mai nem kezelt étel lemondások
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::button
                wire:click="handleAll"
                color="gray"
                :disabled="$unhandledByMeal->isEmpty()"
            >
                Megjelölés kezeltként
            </x-filament::button>
        </x-slot>

        @if(!$unhandledByMeal->isEmpty())
            <div class="flex flex-col gap-2">
                @foreach($unhandledByMeal as $mealData)
                    <div class="flex justify-between gap-4">
                        <x-filament::badge> {{ MealType::from($mealData->meal_type)->getLabel() }}</x-filament::badge>
                        <p class="font-bold">
                            {{ $mealData->total }} db
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">
                Nincs a mai napra kezeletlen étel lemondás. Ha most kezelted a mai lemondásokat, akkor maximum
                10 másodpercen belül frissül a táblázat.
            </p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
