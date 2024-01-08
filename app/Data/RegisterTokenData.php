<?php

namespace App\Data;

use Livewire\Wireable;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class RegisterTokenData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public string $name,
        public string $email
    )
    {
    }
}
