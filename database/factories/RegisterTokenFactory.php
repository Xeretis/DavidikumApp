<?php

namespace Database\Factories;

use App\Models\RegisterToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class RegisterTokenFactory extends Factory
{
    protected $model = RegisterToken::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(), //
            'email' => $this->faker->unique()->safeEmail(),
            'token' => Str::random(32),
            'used_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
