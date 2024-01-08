<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum MealType: string implements HasLabel
{
    case Breakfast = 'breakfast';
    case MorningSnack = 'morning_snack';
    case Lunch = 'lunch';
    case AfternoonSnack = 'afternoon_snack';
    case Dinner = 'dinner';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Breakfast => 'Reggeli',
            self::MorningSnack => 'Tízórai',
            self::Lunch => 'Ebéd',
            self::AfternoonSnack => 'Uzsonna',
            self::Dinner => 'Vacsora',
        };
    }
}
