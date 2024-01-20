<?php

namespace App\Rules;

use App\Settings\MealSettings;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class AfterMealCancellationDeadlineRule implements ValidationRule
{
    private $daysMap = [
        'monday' => 'hétfő',
        'tuesday' => 'kedd',
        'wednesday' => 'szerda',
        'thursday' => 'csütörtök',
        'friday' => 'péntek',
        'saturday' => 'szombat',
        'sunday' => 'vasárnap',
    ];

    function __construct(
        protected string $end_date,
    )
    {

    }

    /**
     * Create a new rule instance.
     * @param string $value
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $this->end_date);

        $dates = CarbonPeriod::create($start_date, $end_date->copy());

        foreach (collect($dates)->take(7) as $date) {
            $dayOfWeek = Str::lower($date->englishDayOfWeek);

            if ($deadline = app(MealSettings::class)->meal_cancellation_deadlines->first(fn($item) => in_array($dayOfWeek, $item->daysOfWeek))) {
                if (now()->isAfter($date->copy()->addHours(-$deadline->hoursBefore))) {
                    $fail("A(z) {$this->daysMap[$dayOfWeek]}i étkezést legalább {$deadline->hoursBefore} órával korábban kell lemondani.");
                }
            }
        }
    }
}
