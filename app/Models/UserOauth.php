<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOauth extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'provider',
        'token',
        'refresh_token',
        'token_secret',
        'expires_at',
        'metadata',
    ];

    protected $hidden = [
        'token',
        'refresh_token',
        'refresh_secret',
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    protected $attributes = [
        'metadata' => '{}',
    ];
}
