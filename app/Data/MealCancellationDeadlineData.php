<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class MealCancellationDeadlineData extends Data
{
    function __construct(
        public array $daysOfWeek,
        public int   $hoursBefore
    )
    {

    }
}
