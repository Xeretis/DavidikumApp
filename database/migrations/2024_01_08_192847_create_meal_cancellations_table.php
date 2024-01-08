<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meal_cancellations', function (Blueprint $table) {
            $table->id();
            $table->string('meals');
            
            $table->date('start_date');
            $table->date('end_date');

            $table->foreignIdFor(User::class, 'requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'handler_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_cancellations');
    }
};
