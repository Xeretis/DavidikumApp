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

    public function getSortOrder(): int
    {
        return match ($this) {
            self::Breakfast => 1,
            self::MorningSnack => 2,
            self::Lunch => 3,
            self::AfternoonSnack => 4,
            self::Dinner => 5,
        };
    }
}
