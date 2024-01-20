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
            'meals' => Arr::random(['breakfast', 'morning_snack', 'lunch', 'afternoon_snack', 'dinner'], $this->faker->numberBetween(1, 5)),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+2 month'),
            'requester_id' => UserFactory::new(),
            'handled_until' => $this->faker->dateTimeBetween('-1 month', '+2 month'),
            'handler_id' => UserFactory::new(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function unhandled(): static
    {
        return $this->state(fn(array $attributes) => [
            'handled_until' => null,
            'handler_id' => null,
        ]);
    }
}
