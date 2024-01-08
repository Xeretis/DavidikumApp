<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('register_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('token')->unique();
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('register_tokens');
    }
};
