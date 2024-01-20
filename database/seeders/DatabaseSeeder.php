<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\MealCancellation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)->create([
            'name' => 'admin',
            'email' => 'admin@test.test',
            'is_admin' => true,
        ]);

        User::factory(20)->unverified()->create();
        User::factory(3)->unverified()->admin()->create();

        MealCancellation::factory(50)->create();
        MealCancellation::factory(10)->unhandled()->create();
    }
}
