<?php

namespace App\Settings;

use App\Data\MealCancellationDeadlineData;
use Spatie\LaravelSettings\Settings;
use Spatie\LaravelSettings\SettingsCasts\CollectionCast;

class MealSettings extends Settings
{
    public bool $is_meal_cancellation_enabled;

    /** @noinspection PhpMissingFieldTypeInspection */
    public $meal_cancellation_deadlines;

    /** @noinspection PhpMissingFieldTypeInspection */
    public $default_meal_amounts;

    public static function group(): string
    {
        return 'admin_settings';
    }

    public static function casts(): array
    {
        return [
            'meal_cancellation_deadlines' => CollectionCast::class . ':' . MealCancellationDeadlineData::class,
        ];
    }
}
