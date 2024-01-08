<?php

namespace App\Models;

use App\Models\Enums\MealType;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'meals',
        'start_date',
        'end_date',
        'requester_id',
        'handler_id',
    ];

    protected $casts = [
        'meals' => AsEnumCollection::class . ':' . MealType::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id');
    }
}
