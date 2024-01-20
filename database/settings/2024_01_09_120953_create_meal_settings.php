<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('admin_settings.is_meal_cancellation_enabled', true);
        $this->migrator->add('admin_settings.meal_cancellation_deadlines', []);
        $this->migrator->add('admin_settings.default_meal_amounts', [
            'breakfast' => 0,
            'morning_snack' => 0,
            'lunch' => 0,
            'afternoon_snack' => 0,
            'dinner' => 0,
        ]);
    }
};
