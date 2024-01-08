<?php

namespace Database\Factories;

use App\Models\MealCancellation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class MealCancellationFactory extends Factory
{
    protected $model = MealCancellation::class;

    public function definition(): array
    {
        return [
            'meals' => Arr::random(['breakfast', 'breakfast_snack', 'lunch', 'afternoon_snack', 'dinner'], $this->faker->numberBetween(1, 3)),
            'start_date' => $this->faker->date('-1 month', '+1 month'),
            'end_date' => $this->faker->date('+1 month', '+2 month'),
            'requester_id' => UserFactory::new(),
            'handler_id' => $this->faker->boolean() ? UserFactory::new() : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
