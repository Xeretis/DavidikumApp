<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class RegisterToken extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'token',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    protected $with = [
        'notifications',
    ];
}
